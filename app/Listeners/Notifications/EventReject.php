<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 7/27/18
 * Time: 10:11 PM
 */

namespace App\Listeners\Notifications;


use App\Models\EventRequest;
use App\Interfaces\INotificationTypes;
use App\Jobs\ProcessNotifications;
use App\Factories\NotificationSenderFactory;
use Illuminate\Support\Facades\Auth;

/**
 * Class EventReject
 * @package App\Listeners
 */
class EventReject
{
    /**
     * @param EventRequest $eventRequest
     * @throws \ReflectionException
     */
    public function handle(EventRequest $eventRequest)
    {
        /**
         * Send notification for event proposal and request creators
         */
        foreach (NotificationSenderFactory::getConstants() as $notificationType) {
            switch ($notificationType) {
                case INotificationTypes::EMAIL:
                    /** Send notification to the user which event request were accepted (normal human) */
                    ProcessNotifications::dispatch([
                        'type' => INotificationTypes::EMAIL,
                        'addressee' => [
                            'email' => $eventRequest->user->email,
                            'email' => 'ego.cogitare@gmail.com',
                            'name' => $eventRequest->user->getFullName(),
                        ],
                        'title' => 'C2Go::Event rejected',
                        'message' => [
                            'template' => 'emails.events.rejected-1',
                            'data' => [
                                'creator' => $eventRequest->proposal->user->getFullName(),
                                'event' => $eventRequest->proposal->event->name,
                                'date' => $eventRequest->proposal->event->date,
                                'destination' => $eventRequest->proposal->event->destination,
                                'requestor' => $eventRequest->user->getFullName(),
                            ]
                        ],
                    ])
                    ->onQueue('emails');

                    /** Send notification to current user (who accept request - disabled human) */
                    ProcessNotifications::dispatch([
                        'type' => INotificationTypes::EMAIL,
                        'addressee' => [
                            'email' => $eventRequest->proposal->user->email,
                            'email' => 'ego.cogitare@yahoo.com',
                            'name' => $eventRequest->proposal->user->getFullName(),
                        ],
                        'title' => 'C2Go::Event rejected',
                        'message' => [
                            'template' => 'emails.events.rejected-2',
                            'data' => [
                                'creator' => $eventRequest->proposal->user->getFullName(),
                                'event' => $eventRequest->proposal->event->name,
                                'date' => $eventRequest->proposal->event->date,
                                'destination' => $eventRequest->proposal->event->destination,
                                'requestor' => $eventRequest->user->getFullName(),
                            ]
                        ],
                    ])
                    ->onQueue('emails');
                    break;

                case INotificationTypes::PUSH:
                    ProcessNotifications::dispatch([
                        'type' => INotificationTypes::PUSH,
                        'addressee' => Auth::user()->id,
                        'title' => 'C2Go::Event rejected',
                        'message' => 'User reject your event request',
                    ])
                    ->onQueue('push');
                    break;

                case INotificationTypes::SMS:
                    break;
            }
        }
    }
}