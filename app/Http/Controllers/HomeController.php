<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', 'month'); // default to month

        // Calculate user activity based on login/logout sessions
        $sessions = \App\Models\UserSession::where('user_id', $user->id)
            ->whereNotNull('login_at')
            ->get();

        $activityData = [];

        foreach ($sessions as $session) {
            $start = $session->login_at;
            $end = $session->logout_at ?? now();

            if ($period === 'week') {
                // Group by week number and year
                $periodKey = $start->format('o-\WW'); // ISO-8601 week date
            } elseif ($period === 'year') {
                // Group by year
                $periodKey = $start->format('Y');
            } else {
                // Default group by month-year
                $periodKey = $start->format('Y-m');
            }

            if (!isset($activityData[$periodKey])) {
                $activityData[$periodKey] = 0;
            }

            // Calculate duration in hours for the session
            $duration = $end->diffInHours($start);
            $activityData[$periodKey] += $duration;
        }

        $labels = [];
        $data = [];

        if ($period === 'week') {
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subWeeks($i);
                $label = 'Week ' . $date->format('W') . ' ' . $date->format('Y');
                $periodKey = $date->format('o-\WW');
                $labels[] = $label;
                $data[] = $activityData[$periodKey] ?? 0;
            }
        } elseif ($period === 'year') {
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subYears($i);
                $label = $date->format('Y');
                $periodKey = $date->format('Y');
                $labels[] = $label;
                $data[] = $activityData[$periodKey] ?? 0;
            }
        } else {
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $label = $date->format('F Y');
                $periodKey = $date->format('Y-m');
                $labels[] = $label;
                $data[] = $activityData[$periodKey] ?? 0;
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        }

        $upcomingEvents = \App\Models\Event::where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        $lastSubscription = \App\Models\CourseSubscription::where('user_id', $user->id)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastLogin = \App\Models\UserSession::where('user_id', $user->id)
            ->whereNotNull('login_at')
            ->orderBy('login_at', 'desc')
            ->first();

        $lastProfileUpdate = $user->updated_at;

        // New counts
        $usersCount = \App\Models\User::count();
        $messagesCount = \App\Models\Message::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })->count();

        $upcomingEventsCount = \App\Models\Event::where('start_time', '>=', now())->count();

        return view('home', compact('labels', 'data', 'period', 'upcomingEvents', 'lastSubscription', 'lastLogin', 'lastProfileUpdate', 'usersCount', 'messagesCount', 'upcomingEventsCount'));
    }

    public function chatMessages(Request $request, $conversationId = null)
    {
        $user = Auth::user();
        $search = $request->input('search');

        if ($conversationId) {
            $messagesQuery = Message::where('conversation_id', $conversationId)
                ->with(['sender', 'receiver', 'conversation'])
                ->orderBy('created_at', 'asc');

            if ($search) {
                $messagesQuery->where('content', 'like', "%{$search}%");
            }

            $messages = $messagesQuery->get();

            $conversation = \App\Models\Conversation::with('users')->findOrFail($conversationId);

            return view('chat.messages', compact('messages', 'conversation', 'search'));
        } else {
            $usersQuery = User::where('id', '!=', $user->id);

            if ($search) {
                $usersQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $usersQuery->get();

            return view('chat.messages', ['users' => $users, 'search' => $search]);
        }
    }

    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        $user = Auth::user();

        // Only allow sender or receiver to delete
        if ($message->sender_id === $user->id || $message->conversation->user_one === $user->id || $message->conversation->user_two === $user->id) {
            $message->delete();
            return redirect()->route('chat.messages', ['conversationId' => $message->conversation_id])->with('success', 'Message deleted successfully.');
        }

        return redirect()->route('chat.messages', ['conversationId' => $message->conversation_id])->with('error', 'Unauthorized action.');
    }

    public function blockUser($userId)
    {
        $user = Auth::user();

        // Simple block implementation: store blocked user ids in session for demo
        $blockedUsers = session()->get('blocked_users', []);
        if (!in_array($userId, $blockedUsers)) {
            $blockedUsers[] = $userId;
            session()->put('blocked_users', $blockedUsers);
        }

        return redirect()->route('chat.messages')->with('success', 'User blocked successfully.');
    }

    public function storeMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // max 10MB
        ]);

        $user = Auth::user();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        if ($request->receiver_id === 'all') {
            // Send message to all users
            $users = User::where('id', '!=', $user->id)->get();
            foreach ($users as $receiver) {
                $conversation = \App\Models\Conversation::whereHas('users', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->whereHas('users', function ($q) use ($receiver) {
                    $q->where('user_id', $receiver->id);
                })->first();

                if (!$conversation) {
                    $conversation = new \App\Models\Conversation();
                    $conversation->type = 'private';
                    $conversation->save();
                    $conversation->users()->attach([$user->id, $receiver->id]);
                }

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'receiver_id' => $receiver->id,
                    'content' => $request->content,
                    'attachment_path' => $attachmentPath,
                ]);
            }
        } else {
            // Single receiver
            $receiverId = $request->receiver_id;
            $conversation = \App\Models\Conversation::whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereHas('users', function ($q) use ($receiverId) {
                $q->where('user_id', $receiverId);
            })->first();

            if (!$conversation) {
                $conversation = new \App\Models\Conversation();
                $conversation->type = 'private';
                $conversation->save();
                $conversation->users()->attach([$user->id, $receiverId]);
            }

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'receiver_id' => $receiverId,
                'content' => $request->content,
                'attachment_path' => $attachmentPath,
            ]);
        }

        return redirect()->route('chat.messages', ['conversationId' => $conversation->id])->with('success', 'Message sent successfully.');
    }

    public function showUserSelection()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('chat.select_user', compact('users'));
    }
    
    public function showSendMessageForm()
    {
        // Deprecated - no longer used
        abort(404);
    }
}
