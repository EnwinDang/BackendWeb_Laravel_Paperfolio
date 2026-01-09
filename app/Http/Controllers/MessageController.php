<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all unique users the current user has conversations with
        $messages = Message::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by other user and get the most recent message
        $conversationsMap = [];
        foreach ($messages as $message) {
            // Determine the other user in the conversation
            $otherUser = $message->sender_id === $user->id 
                ? $message->recipient 
                : $message->sender;
            
            // Skip if relationship not loaded
            if (!$otherUser) {
                continue;
            }
            
            $otherUserId = $otherUser->id;
            
            // Only keep the most recent message per user
            if (!isset($conversationsMap[$otherUserId])) {
                $conversationsMap[$otherUserId] = [
                    'user' => $otherUser,
                    'last_message' => $message,
                ];
            }
        }
        
        // Convert to collection and calculate unread counts
        $conversations = collect($conversationsMap)->map(function($conversation) use ($user) {
            $otherUser = $conversation['user'];
            $conversation['unread_count'] = Message::where('sender_id', $otherUser->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->count();
            return $conversation;
        })->sortByDesc(function($conversation) {
            return $conversation['last_message']->created_at;
        })->values();
        
        // Ensure conversations is always a collection, even if empty
        if ($conversations->isEmpty()) {
            $conversations = collect([]);
        }
        
        return view('messages.index', compact('conversations'));
    }

    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Prevent users from messaging themselves
        if ($currentUser->id === $user->id) {
            return redirect()->route('messages.index')->with('error', 'You cannot message yourself.');
        }
        
        // Prevent admins from being messaged (optional - remove if you want admins to receive messages)
        if ($user->is_admin) {
            return redirect()->route('messages.index')->with('error', 'You cannot message admins.');
        }
        
        // Get all messages between current user and the other user
        $messages = Message::where(function($query) use ($currentUser, $user) {
                $query->where(function($q) use ($currentUser, $user) {
                    $q->where('sender_id', $currentUser->id)
                      ->where('recipient_id', $user->id);
                })->orWhere(function($q) use ($currentUser, $user) {
                    $q->where('sender_id', $user->id)
                      ->where('recipient_id', $currentUser->id);
                });
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Group messages by date
        $messagesByDate = $messages->groupBy(function($message) {
            return $message->created_at->format('Y-m-d');
        });
        
        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('recipient_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        // Get conversations for sidebar
        $allMessages = Message::where(function($query) use ($currentUser) {
                $query->where('sender_id', $currentUser->id)
                      ->orWhere('recipient_id', $currentUser->id);
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $conversationsMap = [];
        foreach ($allMessages as $message) {
            $otherUser = $message->sender_id === $currentUser->id 
                ? $message->recipient 
                : $message->sender;
            
            if (!$otherUser || $otherUser->is_admin) {
                continue;
            }
            
            $otherUserId = $otherUser->id;
            
            if (!isset($conversationsMap[$otherUserId])) {
                $conversationsMap[$otherUserId] = [
                    'user' => $otherUser,
                    'last_message' => $message,
                ];
            }
        }
        
        $conversations = collect($conversationsMap)->map(function($conversation) use ($currentUser) {
            $otherUser = $conversation['user'];
            $conversation['unread_count'] = Message::where('sender_id', $otherUser->id)
                ->where('recipient_id', $currentUser->id)
                ->whereNull('read_at')
                ->count();
            return $conversation;
        })->sortByDesc(function($conversation) {
            return $conversation['last_message']->created_at;
        })->values();
        
        return view('messages.show', compact('user', 'messages', 'messagesByDate', 'conversations'));
    }

    public function store(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        // Prevent users from messaging themselves
        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot message yourself.');
        }
        
        // Prevent admins from being messaged
        if ($user->is_admin) {
            return back()->with('error', 'You cannot message admins.');
        }
        
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);
        
        Message::create([
            'sender_id' => $currentUser->id,
            'recipient_id' => $user->id,
            'message' => $request->message,
        ]);
        
        return redirect()->route('messages.show', $user)->with('success', 'Message sent successfully.');
    }
}
