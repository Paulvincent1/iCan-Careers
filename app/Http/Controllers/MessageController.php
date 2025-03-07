<?php

namespace App\Http\Controllers;

use App\Events\ChatHeadMessage;
use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use function Illuminate\Log\log;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if($request->user()->roles()->first()->name === 'Employer'){
            if(!Gate::allows('employer-profile-check')) {
                return redirect()->back();
            }
        }

        // this code is used when theres a query params user in the url.
        $userDirectMessage = User::where('id', $request->get('user'))->with('sentMessages', 'receivedMessages')->first();


        $user = Auth::user();


        $messages = null;
        $firstMessageChatHead = null;

        if($userDirectMessage){
            if(!$userDirectMessage->sentMessages()->where('receiver_id',$user->id)->first() && !$userDirectMessage->receivedMessages()->where('sender_id',$user->id)->first()){
            //    dd('hi');
                $firstMessageChatHead = [
                    'user' => $userDirectMessage,
                    'latestMessage' => null
                ];
            }

            $messages = Message::where(function ($query) use($user, $userDirectMessage) {

                $query->where('sender_id', $user->id)->where('receiver_id', $userDirectMessage->id);

            })->orWhere(function ($query) use($user, $userDirectMessage) {

                $query->where('receiver_id', $user->id)->where('sender_id', $userDirectMessage->id);

            })->orderBy('created_at','desc')->paginate(20)->withQueryString();

        }else{
            $firstMessageChatHead = null;
        }

        if($request->get('q')){
            $usersId = Message::where('sender_id',$user->id)->whereHas('receiver', function ($query) use($request) {
                $query->where('name', 'like', '%'. $request->get('q') . '%');
            })->pluck('receiver_id')
            ->merge(Message::where('receiver_id',$user->id)->whereHas('sender', function ($query) use($request){
                $query->where('name', 'like', '%'. $request->get('q') . '%');
            })->pluck('sender_id'))->unique()
            ->filter(function ($id) use ($user) {
                return $id != $user->id;
            });
        }else {

            // users that we interact
            $usersId = Message::where('sender_id',$user->id)->pluck('receiver_id')
            ->merge(Message::where('receiver_id',$user->id)->pluck('sender_id'))->unique()
            ->filter(function ($id) use ($user) {
                return $id != $user->id;
            });
        }


        $users = User::whereIn('id',$usersId)->get();
        $chatHeads = [];

        foreach($users as $userchat){
            $sent = $userchat->sentMessages()->latest()->first();
            $received = $userchat->receivedMessages()->latest()->first();

            $latestMessage = null;
            if($sent && $received){
                if($sent->created_at > $received->created_at){

                    $latestMessage = $sent;
                }else{

                    $latestMessage = $received;
                }


            }elseif($sent){

                $latestMessage = $sent;

            }elseif($received){

                $latestMessage = $received;

            }


            $chatHeads[] = [
                'user' => $userchat,
                'latestMessage' => $latestMessage
            ];


        }

        usort($chatHeads, function ($a, $b){
           return $b['latestMessage']->created_at <=> $a['latestMessage']->created_at;
        });

        // dd($chatHeads);



        return inertia('Messages/Messages', ['firstMessageChatHeadProps' => $firstMessageChatHead, 'chatHeadProps' => $chatHeads , 'messageProps' => $messages, 'userDirectMessageProps' => $userDirectMessage]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $receiverId)
    {

        log($request);

        $field = $request->validate([
            'message' => 'required',
        ]);

        $user = Auth::user();
        $message = $user->sentMessages()->create([
            'receiver_id' => $receiverId->id,
            'message' => $field['message'],
        ]);

        log($message->message);
        $message->load('sender');

        broadcast(new MessageSent($message))->toOthers();
        broadcast(new ChatHeadMessage($message))->toOthers();

        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
