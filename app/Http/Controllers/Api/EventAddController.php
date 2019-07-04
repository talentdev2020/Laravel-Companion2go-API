<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventProposal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Events\Add\MeetPlaceRequest;
use App\Http\Requests\Events\Add\PersonalMessageRequest;

class EventAddController extends Controller
{
    /**
     * Event general info (title|description|url) validation
     * @param GeneralInfoRequest $request
     * @return string
     */
    public function general(Requests\Events\Add\GeneralInfoRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->only([
                'title',
                'description',
                'url',
                'event_id'
            ])
        ]);
    }
    
    /**
     * Event category validation
     * @param CategoryRequest $request
     * @return string
     */
    public function category(Requests\Events\Add\CategoryRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->only([
                'category_id'
            ])
        ]);
    }
    
    /**
     * Event date and place validation
     * @param DatePlaceRequest $request
     * @return string
     */
    public function datePlace(Requests\Events\Add\DatePlaceRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->only([
                'timestamp',
                'event_dispatch',
                'event_dispatch_latlng',
                'event_destination',
                'event_destination_latlng',
                'event_meet_place',
                'event_meet_place_latlng',
                'changes',
            ])
        ]);
    }

    /**
     * Event meet place validation
     * @param MeetPlaceRequest $request
     * @return string
     */
    public function meetPlace(MeetPlaceRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->only([
                'meet_place',
            ])
        ]);
    }

    /**
     * Event personal message validation
     * @param PersonalMessageRequest $request
     * @return string
     */
    public function personalMessage(PersonalMessageRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->only([
                'personal_message',
            ])
        ]);
    }
    
    /**
     * Event tickets validation
     * @param TicketsRequest $request
     * @return string
     */
    public function tickets(Requests\Events\Add\TicketsRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->only([
                'bought',
                'price'
            ])
        ]);
    }

    /**
     * Event tickets validation
     * @param TicketsRequest $request
     * @return string
     */
    public function location(Requests\Events\Add\Location $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->only([
                'location_from',
                'location_to',
                'location_from_latlng',
                'location_to_atlng'
            ])
        ]);
    }
    
    /**
     * Event meet place validation
     * @param MeetPlaceRequest $request
     * @return string
     */
    public function add(Requests\Events\Add\AddRequest $request)
    {
        /** @var array $data */
        $data = $request->all();
        
        /** Creating new event scenario */
        if (empty($data['event_id'])) {
            /** @var Event $event */
            $event = Event::create([
                'category_id'        => $data['category_id'],
                'user_id'            => Auth::user()->id,
                'name'               => $data['title'],
                'date'               => Carbon::createFromTimestamp($data['timestamp'])->toDateTimeString(),
                'destination'        => $data['event_destination'],
                'destination_latlng' => isset($data['event_destination_latlng']) ? json_encode($data['event_destination_latlng']) : '{}',
//                'dispatch'           => $data['event_dispatch'],
//                'dispatch_latlng'    => isset($data['event_dispatch_latlng']) ? json_encode($data['event_dispatch_latlng']) : '{}',
                'dispatch' => isset($data['event_dispatch']) ? $data['event_dispatch'] : '',
                'dispatch_latlng' => isset($data['event_dispatch_latlng']) ? json_encode($data['event_dispatch_latlng']) : '{}',
                'description' => $data['description']
            ]);
        } else {
            $event = Event::findOrFail($data['event_id']);
        }
        
        /** If phone number is provided and user have no phone yet */
        if ($data['telephone'] && Auth::user()->getPhone() === '') {
            Auth::user()->setPhone($data['telephone']);
        }
        
        /** @var EventProposal $eventProposal */
        $eventProposal = EventProposal::create([
            'event_id'       => $event->id,
            'user_id'        => Auth::user()->id,
            'tickets_bought' => $data['bought'],
            'price'          => $data['price'],
            'message'        => $data['meet_place'],
            'description'    => $data['description'],
            'url'            => @$data['url'],
//            'personal_message' => @$data['personal_message']
        ]);
        
        return response()->json([
            'success' => true,
            'data' => [
                'event' => $event,
                'proposal' => $eventProposal,
            ]
        ]);
    }
}
