<?php

namespace App\Http\Controllers\Api;


use App\Classes\UserSettingsBase;
use App\Exceptions\WrongSettingsException;
use App\Interfaces\IAccountType;
use App\Interfaces\IState;
use App\Models\User;
use App\Models\UserSetting;
use App\Models\EventRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\IUserSettings;
use App\Models\UserReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Event as EventDispatcher;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends Controller
{

public function sendMail(Request $request) {
	
	 $to = $request->input("email"); 
	    	$from = "admin@automicity.com";
	    $subject = "Form submission";
	    $subject2 = "Copy of your form submission";
	    $message = "<a href='https://c2go.atomicity.pro/register/photo'>Confirm your email from Automicity</a>";
	
	    $headers = "From:" . $from;
	    mail($to,$subject,$message,$headers);
 	     return response()->json([
                'success' => true,
                'message' => 'Mail sent'
            ]);
	
   }


    /**
     * Deactivate user account
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate()
    {
        DB::beginTransaction();
        try {
            /**
             * @var User $user
             */
            $user = User::find(Auth::id());
            $user->deactivated = 1;
            $user->save();

            DB::commit();

            Auth::logout();

            return response()->json([
                'success' => true,
                'message' => 'Account deactivated'
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * @param Request $request
     * @param $provider
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function me()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'status' => true,
                'user_id' => $user->id
            ], 200);
        }

        return response()->json([
            'status' => false,
            'error' => 'UNAUTHORIZED'
        ], 401);
    }
public function sendMail(Request $request) {
    
     $to = $request->input("email"); 
            $from = "admin@automicity.com";
        $subject = "Form submission";
        $subject2 = "Copy of your form submission";
        $message = "<a href='https://c2go.atomicity.pro/register/photo'>Confirm your email from Automicity</a>";
    
        $headers = "From:" . $from;
        mail($to,$subject,$message,$headers);
         return response()->json([
                'success' => true,
                'message' => 'Mail sent'
            ]);
    
   }
  
    /**
     * @param Requests\UpdateUserProgressRequest $request
     * @param $progress
     * @return \Illuminate\Http\JsonResponse
     * @throws WrongSettingsException
     * @throws \ReflectionException
     */
    public function updateProgress(Requests\UpdateUserProgressRequest $request, $progress)
    {
        /** @var array $data Additional request data */
        $data = $request->only(['section', 'value']);

        /** Update registration data */
        if (isset($data['section']) && UserSettingsBase::isSectionValid($data['section'])) {
            UserSetting::apply($data['section'], $data['value']);
        }

        /** Update user registration progress */
        UserSetting::apply(IUserSettings::PROFILE_REGISTRATION_PROGRESS, $progress);

        return response()->json([
            'success' => true,
            'user' => Auth::user(),
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WrongSettingsException
     * @throws \ReflectionException
     */
    public function profilePhoto(Request $request) 
    {
        $storagePath = Storage::getDriver()->getAdapter()->getPathPrefix();
        /** @var string $path */
        $path = $request->file('profile-photo')
            ->store('profile-photos');
        $imageFullPath = "${storagePath}${path}";
        $data = pathinfo($imageFullPath);
        $mimeType = mime_content_type($imageFullPath);
        if (!in_array(@$data['extension'], ['jpeg', 'png', 'jpeg']) ||
            !in_array($mimeType, ['image/jpg', 'image/jpeg', 'image/png'])) {
            Storage::delete($path);
            return response()->json([
                'status' => false,
                'error' => 'File extension invalid'
            ], 400);
        }
        exec("convert -quality 100 -resize 150x150 '${imageFullPath}' '${imageFullPath}'",
            $output, $status);
        if ($status !== 0) {
            Storage::delete($path);
            return response()->json([
                'status' => false,
                'error' => 'Cannot process file'
            ], 400);
        }

        $oldPath = UserSetting::get(IUserSettings::PROFILE_PHOTO);
        Storage::delete($oldPath);

        /** Update user profile photo */
        UserSetting::apply(IUserSettings::PROFILE_PHOTO, $path);

        return response()->json([
            'status' => true,
            'user' => User::find(Auth::user()->id)
        ]);
    }

    
    /**
     * Get user information
     * @return \Illuminate\Http\JsonResponse
     */
    public function currentUser()
    {
        return response()->json([
            'status' => true,
            'data' => Auth::user()
        ]);
    }


    /**
     * @param int $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileInfo(int $user)
    {
        /** @var User $profile */
        $profile = User::with(['reviews' => function($query) {
            $query
                ->with(['reviewer'])
                ->where('is_active', IState::ACTIVE)
                ->orderBy('id', 'DESC')
                ->limit(5);
        }])
        ->find($user);

        if ($profile === null) {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $profile
        ]);
    }


    /**
     * @param Requests\ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Requests\ChangePasswordRequest $request)
    {
        if (\Hash::check($request->input('old_password'), Auth::user()->getAuthPassword()) === false) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'old_password' => ['Old password does not match']
                ]
            ], 422);
        }

        Auth::user()->update(['password' => $request->input('new_password')]);

        return response()->json([
            'success' => true,
            'data' => 'Password changed'
        ]);
    }


    /**
     * @param Requests\ChangeEmailRequest $request
     * @param string|null $hash
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function changeEmail(Requests\ChangeEmailRequest $request, $hash = null)
    {
        $request->request->add([
            'hash' => $hash
        ]);

        /** Broadcast email change event (to send notifications) */
        $result = EventDispatcher::fire('email.change', [$request->all()]);

        return response()->json([
            'success' => true,
            'data' => $result[0]
        ]);
    }


     public function changePhone(Request $request, $hash = null)
    {
        
        /** Broadcast email change event (to send notifications) */
        UserSetting::apply(
            IUserSettings::PROFILE_PHONE,
            $request->input('phone', '')
        );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WrongSettingsException
     * @throws \ReflectionException
     */
    public function updateDisabilityInfo(Request $request)
    {
        $request->validate([
            'disability_information' => 'required|string|min:10'
        ]);

        UserSetting::apply(
            IUserSettings::PROFILE_DISABILITY_INFORMATION,
            $request->input('disability_information')
        );

        return response()->json([
            'success' => true,
            'message' => 'Information updated',
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WrongSettingsException
     * @throws \ReflectionException
     */
    public function updateRequireAssistance(Request $request)
    {
        $request->validate([
            'require_assistance' => 'required|string|min:10'
        ]);

        UserSetting::apply(
            IUserSettings::PROFILE_REQUIRE_ASSISTANCE,
            $request->input('require_assistance')
        );

        return response()->json([
            'success' => true,
            'message' => 'Information updated',
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WrongSettingsException
     * @throws \ReflectionException
     */
    public function updateSettingSection(Request $request)
    {
        UserSetting::apply(
            $request->input('section'),
            $request->input('value')
        );

        return response()->json([
            'success' => true,
            'message' => 'Information updated',
        ]);
    }


    /**
     * @param Requests\UpdateSettingsRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WrongSettingsException
     * @throws \ReflectionException
     */
    public function updateSettings(Requests\UpdateSettingsRequest $request)
    {
        /** Update home latitude and longtitude */
        if (!empty($request->input('home_address_latlng'))) {
            UserSetting::apply(
                IUserSettings::PROFILE_HOME_ADDRESS_LAT_LNG,
                json_encode($request->input('home_address_latlng'))
            );
        }
     
        /** Update user birth date */
        if ($request->has('birth_date'))
            UserSetting::apply(
                IUserSettings::PROFILE_BIRTH_DATE,
                $request->input('birth_date')
            );

        /** Update user friendly home address */

        if ($request->has('home_address'))
            UserSetting::apply(
                IUserSettings::PROFILE_HOME_ADDRESS_FRIENDLY,
                $request->input('home_address')
            );

        if ($request->has('phone')) {
            UserSetting::apply(
                IUserSettings::PROFILE_PHONE,
                $request->input('phone')
            );;
        }

        /** Update user postcode */
        if ($request->has('postcode'))
            UserSetting::apply(
                IUserSettings::PROFILE_HOME_POSTCODE,
                $request->input('postcode')
            );

        /** Update first and last name */
        if ($request->has('first_name')
            && $request->has('last_name'))
            Auth::user()->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated',
        ]);
    }
    public function storeFeedback(Requests\VoteRequest $request, $requestId)
    {
        /** @var EventRequest|null $eventRequest */
        $eventRequest = EventRequest::with(['proposal'])
            ->where('id', $requestId)
            ->first();
        if ($eventRequest === null) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }
            DB::table("user_reviews")->where("id",$requestId)->update(array("message"=>$request->input("message")));
            return response()->json([
                'success' => true,
                'message' => 'Your vote saved'
            ]);
     //   }
        
    }

    /**
     * @param Requests\VoteRequest $request
     * @param $requestId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeVote(Requests\VoteRequest $request, $requestId)
    {
        /** @var EventRequest|null $eventRequest */
        $eventRequest = EventRequest::with(['proposal'])
            ->where('id', $requestId)
            ->where('is_active', IState::ACTIVE)
            ->first();

        if ($eventRequest === null) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        /**
         * Check if user has anought rights to vote on this event request
         * @var array|bool $reviewGate
         */
        if (Gate::allows('vote:write', $eventRequest)) {
            /** @var int $userAboutId */
            $userAboutId = Auth::user()->getAccountType() === IAccountType::NORMAL
                ? $eventRequest->proposal->user_id
                : $eventRequest->user_id;

            /** @var UserReview $userReview */
            $userReview = UserReview::create([
                'mark' => $request->input('mark'),
                'user_about_id' => $userAboutId,
                'user_id' => Auth::user()->id,
                'event_id' => $eventRequest->proposal->event->id,
            ]);

            /** Broadcast vote received event (to send notifications) */
            EventDispatcher::fire('vote.lived', $userReview);

            return response()->json([
                'success' => true,
                'message' => 'Your vote saved'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'You have no right to live vote for this user and event'
        ], 403);
    }

    public function facebookAvatar(Request $request) {
        $token = $request->get("token");
        $client = new Client();
        $response = $client->get("https://graph.facebook.com/v2.7/me/picture?access_token=$token");
        $name = 'profile-photos/' . Auth::id() . '.jpg';
        Storage::put($name, $response->getBody(), 'public');
        /** Update user profile photo */
        UserSetting::apply(IUserSettings::PROFILE_PHOTO, $name);

        return response()->json([
            'status' => true,
            'user' => User::find(Auth::user()->id)
        ]);
    }
}
