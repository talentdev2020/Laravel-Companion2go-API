<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/check', 'HomeController@check')->name('home_check');
Route::get('/home/test', 'HomeController@test')->name('home_test');

Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::group(['namespace' => 'Auth'], function () {
        Route::get('/login', 'AuthController@showLoginForm')->name('admin.login');
        Route::post('/login', 'AuthController@login')->name('admin.login.submit');
    });
    Route::group(['middleware'=>'auth:admin'], function () {
        Route::get('/logout', function() {
            Auth::logout();
            return redirect()->intended(route('admin.login'));
        });
        Route::post('/file/upload', 'FileController@upload')->name('admin.file.upload');
        Route::get('/', 'IndexController@index')->name('admin.dashboard');
        Route::get('/report/{type}', 'ReportController@index');
        Route::resource('/pages', 'PagesController');
        Route::resource('/admins', 'AdminsController');
        Route::resource('/users', 'UsersController');
        Route::resource('/categories', 'CategoriesController');
        Route::resource('/events', 'EventsController');
        Route::resource('/faqs', 'FAQsController');
        Route::resource('/testimonials', 'TestimonialsController');
    });
});