<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Dialog;
use App\Models\EventRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use DB;

/**
 * Class EventsController
 * @package App\Http\Controllers\Api
 */
class MessageController extends Controller
{

    /**
     * Send message
     * @return \Illuminate\View\View
     */
    public function send(Request $request)
    {
        $user = User::find(Auth::id());
        /**
         * @var Dialog $dialog
         */
        DB::beginTransaction();

        try {
            $dialog = Dialog::with(['members'])->find($request->get('dialog_id', -1));
            $exists = $dialog->members()
                ->where('user_id', '=', $user->id)
                ->count();
            if ($exists === 0) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'You are not member of the dialog'
                ]);
            }

            $dialog->last_time = \Carbon\Carbon::now();
            $dialog->last_message = $request->get('message');
            $dialog->save();

            $message = new Message([
                'value' => $request->get('message'),
                'sender_id' => $user->id,
            ]);
            $dialog->messages()->save($message);
            DB::commit();


            Redis::publish('messages', json_encode([
                'dialog' => $dialog,
                'message' => $message
            ]));

            return response()->json([
                'success' => true,
                'data' => $message
            ]);

        } catch (\Exception $ex) {
            DB::rollBack();
        }
    }

    /**
     * Send message
     * @return \Illuminate\View\View
     */
    public function dialogs()
    {
        $dialogs = Dialog::with('members')
            ->whereHas('members', function($q) {
                $q->where('user_id', '=', Auth::id());
            });

        return response()->json([
            'status' => true,
            'data' => $dialogs->map(function($dialog) {
                return [
                    'id' => $dialog->id,
                    'name' => $dialog->name,
                    'last_message' => $dialog->last_message,
                    'last_time' => $dialog->last_time,
//                    'members' => $dialog->members
                ];
            })
        ]);
    }

    /**
    * Send message
    * @return \Illuminate\View\View
    */
    public function dialog($id, Request $request)
    {
        if ($request->get('request', false)) {
            $dialog = Dialog::find($id);
        } else {
            $dialog = EventRequest::where('event_proposals_id', '=', $id)
                ->first()
                ->dialog()
                ->first();
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $dialog->id,
                'name' => $dialog->name,
                'last_message' => $dialog->last_message,
                'last_time' => $dialog->last_time,
                'members' => $dialog->members()->get()
            ]
        ]);
    }

    /**
     * Send message
     * @return \Illuminate\View\View
     */
    public function createDialog(Request $request)
    {
        DB::beginTransaction();
        try {
            $message = new Message([
                'sender_id' => Auth::id(),
                'value' => $request->get('message')
            ]);

            $dialog = new Dialog([
                'name' => $request->get('name', 'No title'),
                'last_message' => $request->get('message'),
                'last_time' => \Carbon\Carbon::now()
            ]);
            $dialog->save();
            $dialog->messages()->save($message);

            $sender = User::find(Auth::id());
            $receiver = User::find($request->get('receiver_id', -1));

            $dialog->members()->save($sender);
            $dialog->members()->save($receiver);

            DB::commit();

            $data = [
                'receivers' => $dialog->members()->get()->map(function (User $member) {
                    return $member->id;
                }),
                'sender' => [
                    'id' => $sender->id,
                    'name' => $sender->getFullName()
                ],
                'dialog' => [
                    'id' => $dialog->id,
                    'name' => $dialog->name
                ],
                'value' => $message->value
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
        }
    }

    public function messages(Dialog $dialog)
    {
        $exists = $dialog->members()
            ->where('user_id', '=', Auth::id())
            ->count();

        if ($exists === 0) {
            return response()->json([
                'status' => false,
                'message' => 'You are not member of the dialog'
            ]);
        }

        $messages = Message::with(['sender'])
            ->where('dialog_id', '=', $dialog->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }
}