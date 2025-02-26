<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       
        $userDirectMessage = User::where('id', $request->get('user'))->with('sentMessages', 'receivedMessages')->first();

        
        $user = Auth::user();


        $messages = null;
        $firstMessageChatHead = null;
      
        if($userDirectMessage){
            if(!$userDirectMessage->sentMessages()->first() && !$userDirectMessage->receivedMessages()->first()){            
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

            })->get();
            
        }else{
            $firstMessageChatHead = null;
        }


        // users that we interact
        $usersId = Message::where('sender_id',$user->id)->pluck('receiver_id')
        ->merge(Message::where('receiver_id',$user->id)->pluck('sender_id'))->unique()
        ->filter(function ($id) use ($user) {
            return $id != $user->id;
        });

        $users = User::whereIn('id',$usersId)->get();
        $chatHeads = [];

        foreach($users as $user){
            $sent = $user->sentMessages()->latest()->first();
            $received = $user->receivedMessages()->latest()->first();

            $latestMessage = null;
            if($sent && $received){
                if($sent->created_at > $received->created_at ){

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
                'user' => $user,
                'latestMessage' => $latestMessage
            ];


        }

        usort($chatHeads, function ($a, $b){
           return $b['latestMessage']->created_at <=> $a['latestMessage']->created_at;
        });

        // dd($chatHeads);



        return inertia('Messages/Messages', ['firstMessageChatHeadProps' => $firstMessageChatHead, 'chatHeadProps' => $chatHeads , 'messageProps' => $messages]);
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
        $field = $request->validate([
            'message' => 'required',
        ]);
        
        $user = Auth::user();
        $message = $user->sentMessages()->create([
            'receiver_id' => $receiverId->id,
            'message' => $field['message'],
        ]);
        
        broadcast($message)->toOthers();

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
