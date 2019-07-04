<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 7/27/18
 * Time: 10:11 PM
 */

namespace App\Listeners;


use App\Interfaces\IEventStates;
use App\Interfaces\IAccountType;
use App\Interfaces\IState;
use Illuminate\Support\Facades\Auth;
use App\Models\EventRequest;
use App\Models\EventProposal;
use App\Models\Dialog;
use App\Models\Message;
use App\Models\User;
use Event as EventDispatcher;
use DB;

/**
 * Class EventAccept
 * @package App\Listeners
 */
class EventAccept
{
    /**
     * @param EventRequest $eventRequest
     * @throws \Exception
     */
    public function handle(EventRequest $eventRequest, $message)
    {
        if (Auth::user()->getAccountType() !== IAccountType::DISABLED) {
            throw new \Exception('Only disabled people can accept event requests.');
        }

        DB::beginTransaction();

        /** Only active and new requests can be accepted */
        if ($eventRequest->is_active !== IState::ACTIVE || $eventRequest->state !== IEventStates::STATE_NEW) {
            throw new \Exception('Event request is not active anymore.');
        }

        /** Only requests for self-created, active proposals can be accepted */
        if ($eventRequest->proposal === null ||
//            $eventRequest->proposal->is_active !== IState::ACTIVE ||
            $eventRequest->proposal->user_id !== Auth::user()->id
        ) {
            throw new \Exception('Event proposal does not exists.');
        }

        /** Mark event request as accepted */
        $eventRequest->update([
            'state' => IEventStates::STATE_ACCEPTED,
        ]);

        /** Mark all requests for the same event as "Rejected" */
        EventRequest::where('event_proposals_id', $eventRequest->event_proposals_id)
            ->where('id', '<>', $eventRequest->id)
            ->update([
                'state' => IEventStates::STATE_REJECTED
            ]);

        /** Hide event proposal */
        EventProposal::where('id', $eventRequest->event_proposals_id)
            ->update([
                'is_active' => IState::INACTIVE
            ]);

        $dialog = new Dialog([
            'request_id' => $eventRequest->id,
            'name' => $eventRequest->proposal->event->name,
            'last_message' => $message,
            'last_time' => \Carbon\Carbon::now()
        ]);
        $dialog->save();


        $dialog->members()->saveMany([
            User::find(Auth::id()),
            User::find($eventRequest->user_id)
        ]);

        $dialog->messages()->save(new Message([
            'sender_id' => $eventRequest->user_id,
            'value' => $eventRequest->message
        ]));

        $dialog->messages()->save(new Message([
            'sender_id' => Auth::id(),
            'value' => $message
        ]));

        DB::commit();

        /** Broadcast notification send event */
        EventDispatcher::fire('event.accept.notification', $eventRequest);
    }
}