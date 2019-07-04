<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 7/27/18
 * Time: 10:11 PM
 */

namespace App\Listeners;


use App\Models\UserReview;
use App\Factories\NotificationSenderFactory;
use App\Interfaces\INotificationTypes;
use App\Jobs\ProcessNotifications;

/**
 * Class VoteLived
 * @package App\Listeners
 */
class VoteLived
{
    /**
     * @param UserReview $userReview
     * @throws \ReflectionException
     */
    public function handle(UserReview $userReview)
    {
        /**
         * Send notification for event proposal and request creators
         */
        foreach (NotificationSenderFactory::getConstants() as $notificationType) {
            switch ($notificationType) {
                case INotificationTypes::EMAIL:
                    /** Send notification to the user about this notification was written */
                    ProcessNotifications::dispatch([
                        'type' => INotificationTypes::EMAIL,
                        'addressee' => [
                            'email' => $userReview->userAbout->email,
                            'email' => 'ego.cogitare@gmail.com',
                            'name' => $userReview->userAbout->getFullName(),
                        ],
                        'title' => 'C2Go::You got a review',
                        'message' => [
                            'template' => 'emails.reviews.vote',
                            'data' => [
                                'reviewer' => $userReview->reviewer,
                                'event' => $userReview->event,
                                'userAbout' => $userReview->userAbout,
                                'mark' => $userReview->mark,
                            ]
                        ],
                    ])
                    ->onQueue('emails');
                    break;

                case INotificationTypes::SMS:
                    break;

                case INotificationTypes::PUSH:
                    break;
            }
        }
    }
}