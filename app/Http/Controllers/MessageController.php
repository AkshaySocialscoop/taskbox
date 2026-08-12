<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $authId = auth()->id();

        // 🔥 STEP 1: Mark incoming messages as READ
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // 🔥 STEP 2: Fetch conversation messages
        $messages = Message::where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)
                ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)
                ->where('receiver_id', $authId);
            })
            ->orderBy('created_at')
            ->get();

        // 🔥 STEP 3: Return JSON
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'photo' => $user->userinfo?->profile_photo
                    ? asset('storage/' . $user->userinfo->profile_photo)
                    : asset('assets/images/avatars/01.png'),
            ],
            'messages' => $messages
        ]);
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
    public function store(Request $request)
    {
      $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json(['success' => true]);
    }
    
    public function unreadCounts()
    {
        $authId = auth()->id();

        $counts = Message::selectRaw('sender_id, COUNT(*) as total')
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->pluck('total', 'sender_id');

        return response()->json($counts);
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
