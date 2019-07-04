<?php

namespace App\Http\Controllers\Api;


use App\Components\NotificationSender;
use App\EmailConfirmationToken;
use App\Interfaces\INotificationTypes;
use App\Interfaces\IUserSettings;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = array_merge(
            ['is_blocked' => false],
            $request->only('email', 'password')
        );

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            $user = User::find(JWTAuth::toUser($token)->id);
            $user->deactivated = false;
            $user->save();
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token', 'user'));
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        //$notifications = Notification::counts($user);

        //return response()->json(compact('user', 'notifications'));
        return response()->json(compact('user'));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        try {
            if (!$token = JWTAuth::refresh($request->token)) {
                return response()->json(['error' => 'token_invalid'], 400);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('token'));
    }


    /**
     * Check if all required field for registration with email are correct
     * @param Requests\AuthRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userValidation(Requests\AuthRegistrationRequest $request)
    {
        return response()->json(['valid' => true]);
    }


    /**
     * Registration via email
     * @param Requests\AuthRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\WrongSettingsException
     * @throws \ReflectionException
     */
    public function registration(Requests\AuthRegistrationRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();

            if ($data['method'] === 'FACEBOOK') {
                $data['password'] = $data['facebook_external_id'];
            }

            /**
             * @var User $user
             */
            $user = User::create($data);
            $token = JWTAuth::fromUser($user);
            $user = User::find($user->id);

            /** Save user location and birth date data to user settings table */
            UserSetting::apply(IUserSettings::PROFILE_REGISTRATION_PROGRESS, 2, $user->id);
            UserSetting::apply(IUserSettings::PROFILE_HOME_ADDRESS_FRIENDLY, $data['home_address'], $user->id);
            UserSetting::apply(IUserSettings::PROFILE_HOME_ADDRESS_LAT_LNG, json_encode($data['location']), $user->id);
            UserSetting::apply(IUserSettings::PROFILE_BIRTH_DATE, $data['birth_date'], $user->id);
            UserSetting::apply(IUserSettings::PROFILE_IS_SUBSCRIBED, $data['is_subscribed'], $user->id);
            if (!empty($data['facebook_external_id'])) {
                UserSetting::apply(IUserSettings::FACEBOOK_NETWORK_ID, $data['facebook_external_id'], $user->id);
            }

            $token = uniqid();
            $link = env('APP_FRONT_URL') . '/email-confirmation/' . $token;

            EmailConfirmationToken::create([
                'user_id' => $user->id,
                'token' => $token
            ]);

            DB::commit();

            (NotificationSender::getInstance(INotificationTypes::EMAIL))
                ->setTitle('Registration')
                ->setMessage("Please, confirm your email address. ${link}")
                ->setAddressee([
                    'email' => $user->email,
                    'name' => $user->first_name
                ])
                ->send();

            if ($data['method'] === 'FACEBOOK') {
                $credentials = array_merge(
                    [
                        'is_blocked' => false,
                        'password' => $data['facebook_external_id']
                    ],
                    $request->only('email')
                );
            } else {
                $credentials = array_merge(
                    [
                        'is_blocked' => false,
                    ],
                    $request->only('email', 'password')
                );
            }

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            $user = User::find(JWTAuth::toUser($token)->id);

            return response()->json(compact('token', 'user'));
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public function confirmEmail($token) {
        DB::beginTransaction();
        try {
            /**
             * @var EmailConfirmationToken $emailToken
             */
            $emailToken = EmailConfirmationToken::with('user')->where('token', '=', $token)
                ->first();
            if (empty($emailToken)) {
                throw new NotFoundHttpException("Token not found");
            }

            /**
             * @var User $user
             */
            $user = $emailToken->user()->first();
            $user->email_confirmed = true;
            $user->save();

            $emailToken->delete();

            DB::commit();
            return \response()->json([]);
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * @param Requests\AuthForgotRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Requests\AuthForgotRequest $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        $password = str_random(6);
        $user->password = $password;
        $user->save();
        try {
            $status = true;
        } catch (\Exception $ex) {
            Log::error($ex);
            $status = false;
        }
        return response()->json(['status' => $status]);
    }


    /**
     * Redirect the user to the GitHub authentication page.
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider(Request $request, $provider)
    {
        return Socialite::driver($provider)->redirect();
    }


    /**
     * @param Request $request
     * @param $provider
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function social(Request $request, $provider)
    {
        $socialUser = Socialite::driver($provider)->userFromToken($request->input('token'));
        if ($socialUser) {
            $user = User::where('email', $socialUser->email)->first();
            if ($user) {
                $token = JWTAuth::fromUser($user, ['is_blocked' => false]);
                //$notifications = Notification::counts($user);
                //return response()->json(compact('token', 'user', 'notifications'));
                return response()->json(compact('token', 'user'));
            }
        }
        return new Response([
            'status' => false,
            'error' => 'Forbidden'
        ], 403);
    }

}
