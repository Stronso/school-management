<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function conversations(Request $request)
    {
        $conversations = $request->user()->conversations()
            ->with(['users' => function ($query) use ($request) {
                $query->where('users.id', '!=', $request->user()->id);
            }])
            ->withCount(['messages' => function ($query) use ($request) {
                $query->whereNull('read_at')
                    ->where('sender_id', '!=', $request->user()->id);
            }])
            ->latest()
            ->get();

        return response()->json($conversations);
    }

    public function createConversation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'name' => 'required_if:type,group|string|max:255',
            'type' => 'required|in:private,group',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // For private conversations, check if one already exists
        if ($request->type === 'private' && count($request->user_ids) === 1) {
            $existingConversation = Conversation::whereHas('users', function ($query) use ($request) {
                $query->where('users.id', $request->user_ids[0]);
            })->whereHas('users', function ($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })->where('type', 'private')
            ->first();

            if ($existingConversation) {
                return response()->json($existingConversation);
            }
        }

        $conversation = Conversation::create([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        $userIds = array_merge($request->user_ids, [$request->user()->id]);
        $conversation->users()->attach($userIds);

        return response()->json($conversation->load('users'), 201);
    }

    public function messages(Request $request, Conversation $conversation)
    {
        if (!$conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->latest()
            ->paginate(50);

        // Mark messages as read
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        if (!$conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $messageData = [
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'content' => $request->content,
        ];

        if ($request->hasFile('attachment')) {
            $messageData['attachment_path'] = $request->file('attachment')->store('chat_attachments');
        }

        $message = Message::create($messageData);

        return response()->json($message->load('sender'), 201);
    }

    public function addUsers(Request $request, Conversation $conversation)
    {
        if (!$conversation->users->contains($request->user()->id) || $conversation->type !== 'group') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation->users()->attach($request->user_ids);

        return response()->json($conversation->load('users'));
    }

    public function removeUser(Request $request, Conversation $conversation, User $user)
    {
        if (!$conversation->users->contains($request->user()->id) || $conversation->type !== 'group') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->users()->detach($user->id);

        return response()->json(['message' => 'User removed from conversation']);
    }

    public function leaveConversation(Request $request, Conversation $conversation)
    {
        if (!$conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->users()->detach($request->user()->id);

        // Delete the conversation if no users left
        if ($conversation->users()->count() === 0) {
            $conversation->delete();
        }

        return response()->json(['message' => 'Left conversation successfully']);
    }
} 