<?php

use App\Http\Middleware\CheckRegCompleteness;
use App\Http\Middleware\DisabledUsersFilter;
use App\Http\Middleware\NormalUsersFilter;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api'], function () {
    /**
     * Not logged in user requests
     */
    Route::get('/categories', 'CategoriesController@index');
    Route::get('/category/{category}', 'CategoriesController@show');
    Route::group(['prefix' => 'events'], function() {
        Route::get('/', 'EventsController@index');
        Route::get('/proposals/{id}', 'EventsController@proposals');
        Route::get('/details/{proposal}', 'EventsController@details');
        Route::get('/general/{proposal}', 'EventsController@general');
        Route::get('/autocomplete', 'EventsController@autocomplete');
    });
    Route::post('/user/change-email/{hash}', 'UserController@changeEmail');


    Route::group(['prefix' => 'auth'], function() {
        Route::post('/login', 'AuthController@login')->name('auth_login');
        Route::post('/email-confirmation/{token}', 'AuthController@confirmEmail');
        Route::get('/refresh-token', 'AuthController@refreshToken');
        Route::post('/user-validation', 'AuthController@userValidation');
        Route::post('/registration', 'AuthController@registration')->name('auth_registration');
        Route::post('/forgot-password', 'AuthController@forgotPassword');
        Route::group(['middleware' => ['jwt.auth',]], function () {
            Route::get('/user', 'AuthController@getAuthenticatedUser')->name('auth_getAuthenticatedUser');
            Route::resource('user', 'UserController', ['only' => [
                'show', 'update', 'destroy'
            ]]);
        });
        Route::group(['middleware' => ['web']], function() {
            Route::post('/{provider}', 'AuthController@social');
            Route::get('/{provider}', 'AuthController@redirectToProvider');
        });
    });


    Route::group(['prefix' => 'proposal'], function() {

        Route::get('/view/{proposal}', 'ProposalController@view');
        Route::get('/all', 'ProposalController@viewAll');

        Route::group(['middleware' => ['jwt.auth']], function() {
            Route::post('/store', 'ProposalController@store');
            Route::delete('/delete/{proposal}', 'ProposalController@delete');
        });
    });
  Route::post('/sendMail','UserController@sendMail');
    /**
     * Need authorization to perform a request
     */
    Route::group(['middleware' => ['jwt.auth']], function() {


        Route::group(['prefix' => 'user'], function() {

	  
            Route::get('/me', 'UserController@me');
            Route::get('/deactivate', 'UserController@deactivate');
            Route::post('/get-facebook-img', 'UserController@facebookAvatar');
            Route::get('/info', 'UserController@currentUser');
            Route::get('/{user}/info', 'UserController@profileInfo');
            Route::post('/progress/{progress}', 'UserController@updateProgress');
            Route::post('/profile-photo', 'UserController@profilePhoto');
            Route::post('/deactivate', 'UserController@deactivate');
            Route::post('/change-password', 'UserController@changePassword');
            Route::post('/change-email', 'UserController@changeEmail');
            Route::post('/change-phone', 'UserController@changePhone');
            Route::post('/disability-information', 'UserController@updateDisabilityInfo');
            Route::post('/require-assistance', 'UserController@updateRequireAssistance');
            Route::post('/update-setting', 'UserController@updateSettingSection');
            Route::post('/update-settings', 'UserController@updateSettings');

            /** Live vote after event */
            Route::post('/vote/{requestId}', 'UserController@storeVote');
            Route::post('/feedback/{requestId}', 'UserController@storeFeedback');
        });

        Route::group(['prefix' => 'message'], function() {
            Route::post('/send', 'MessageController@send');
            Route::get('/dialogs', 'MessageController@dialogs');
            Route::get('/dialog/{id}', 'MessageController@dialog');
            Route::post('/create-dialog', 'MessageController@createDialog');
            Route::get('/messages/{dialog}', 'MessageController@messages');
        });
	
        Route::group(['middleware' => CheckRegCompleteness::class], function() {
            Route::group(['prefix' => 'events'], function() {
                /** Get current logged in user event requests */
                Route::get('/requests', 'EventsController@showUserRequests');

                Route::get('/request/exists', 'EventsController@hasRequest');
                
                /** Make event request to the user proposal */
                Route::group(['middleware' => NormalUsersFilter::class], function() {
                    Route::post('/requests/{proposal}', 'EventsController@storeRequest');
                });
                 Route::post('/accept/{proposal}', 'EventsController@acceptRequest');
                Route::post('/reject/{proposal}', 'EventsController@rejectRequest');
                /** Get current logged in user event requests */
                Route::get('/visited', 'EventsController@visitedEvents');
               
                /** Add new event */
                Route::group(['middleware' => DisabledUsersFilter::class], function() {
                    /** Event add validation URLs */
                    Route::post('/add/general', 'EventAddController@general');
                    Route::post('/add/category', 'EventAddController@category');
                    Route::post('/add/date-place', 'EventAddController@datePlace');
                    Route::post('/add/tickets', 'EventAddController@tickets');
                    Route::post('/add/meet-place', 'EventAddController@meetPlace');
                    Route::post('/add/location', 'EventAddController@location');
                    Route::post('/add/personal-message', 'EventAddController@personalMessage');
                    Route::post('/add', 'EventAddController@add');
                    
                    /** Accept event request */
                    Route::post('/request/{requestId}/accept', 'EventsController@eventAccept');

                    /** Decline event request */
                    Route::post('/request/{requestId}/reject', 'EventsController@eventReject');
                    
                    /** Show event request details */
                    Route::get('/requests/{requestId}/overview', 'EventsController@requestOverview');
                });
                
                /** Get upcoming (future) events list */
                Route::get('/upcoming', 'EventsController@upcomingEvents');
            });
        });
    });
});

