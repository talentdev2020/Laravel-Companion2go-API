<?php

namespace App\Providers;


use App\Interfaces\IAccountType;
use App\Interfaces\IEventStates;
use App\Interfaces\IState;
use App\Proposal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\EventRequest;
use App\Models\User;
use App\Models\UserReview;
use Carbon\Carbon;
use Exception;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [
        'App\Models\EventRequest' => 'App\Policies\VotePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     * @return boolean
     * @throws Exception
     */
    public function boot()
    {
        $this->registerPolicies();

        /**
         * Check if user has permissions to live vote after event
         */
        Gate::define('vote:write', function (User $user, EventRequest $eventRequest) {
            /** If user account type not defined */
            if (!in_array($user->getAccountType(), [IAccountType::DISABLED, IAccountType::NORMAL], true)) {
                throw new Exception('Unsupported user account type');
            }

            /** Check if event request was accepted */
            if ($eventRequest === null || $eventRequest->state !== IEventStates::STATE_ACCEPTED) {
                return false;
            }

            /** Check rights for disabled people */
            if ($user->getAccountType() === IAccountType::DISABLED) {
                if ($eventRequest->proposal === null || $eventRequest->proposal->is_active !== IState::INACTIVE ||
                    $eventRequest->proposal->user_id !== $user->id) {
                    return false;
                }
                /** @var int $userAboutId User id the comment should written about */
                $userAboutId = $eventRequest->user_id;
            }

            /** Check rights for normal people */
            if ($user->getAccountType() === IAccountType::NORMAL) {
                if ($eventRequest->user_id !== $user->id || $eventRequest->is_active !== IState::ACTIVE) {
                    return false;
                }
                /** @var int $userAboutId User id the comment should written about */
                $userAboutId = $eventRequest->proposal->user_id;
            }

            /** Check if event ends at least 1 day ago */
            if ($eventRequest->proposal->event === null || Carbon::now()->diffInDays(Carbon::parse($eventRequest->proposal->event->date)) < 1) {
                return false;
            }

            /**
             * Check if user not leave vote yet for this user and event
             * @var UserReview $review
             */
            $review = UserReview::where('user_id', $user->id)
                ->where('user_about_id', $userAboutId)
                ->where('event_id', $eventRequest->proposal->event->id)
                ->first();

            if ($review !== null) {
                return false;
            }

            return true;
        });

        Gate::define('delete-proposal', function(User $user,Proposal $proposal) {
            return $user->id === $proposal->user_id;
        });

        Gate::define('store-proposal', function(User $user) {
            return $user->getAccountType() === 1;
        });
    }
}
