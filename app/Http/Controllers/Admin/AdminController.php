<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Message;
use App\Models\ChatbotResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users_count = User::count();
        $courses_count = Course::count();
        $messages_count = ChatMessage::count();

        // Get course distribution by category or subject
        $courseDistribution = Course::select('category', \DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        // Prepare data for chart
        $labels = $courseDistribution->pluck('category')->toArray();
        $data = $courseDistribution->pluck('total')->toArray();

        return view('admin.dashboard', compact('users_count', 'courses_count', 'messages_count', 'labels', 'data'));
    }

    // User Management
    public function users(Request $request)
    {
        $search = $request->input('search');

        $usersQuery = User::query();

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->paginate(10)->appends(['search' => $search]);

        return view('admin.users.index', compact('users', 'search'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,teacher,student'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student'
        ]);

        $user->update($validated);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function deleteUser(User $user)
    {
        // Delete related messages where user is sender or receiver
        \App\Models\Message::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    // Course Management
    public function courses()
    {
        $courses = Course::with('teacher')->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function createCourse()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.courses.create', compact('teachers'));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'teacher_id' => 'required|exists:users,id'
        ]);

        Course::create($validated);

        return redirect()->route('admin.courses')->with('success', 'Course created successfully');
    }

    // Chat Management
    public function chatMessages(Request $request)
    {
        $search = $request->input('search');

        $messagesQuery = \App\Models\Message::with(['sender', 'receiver'])->latest();

        if ($search) {
            $messagesQuery->whereHas('sender', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })->orWhereHas('receiver', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })->orWhere('content', 'like', "%{$search}%");
        }

        $messages = $messagesQuery->paginate(20)->appends(['search' => $search]);

        return view('admin.chat.messages', compact('messages', 'search'));
    }

    public function deleteMessage(ChatMessage $message)
    {
        $user = auth()->user();

        // Authorization: allow if user is admin or sender of the message
        if ($user->role === 'admin' || $user->id === $message->sender_id) {
            $message->delete();
            return redirect()->route('admin.chat.messages')->with('success', 'Message deleted successfully');
        }

        return redirect()->route('admin.chat.messages')->with('error', 'Unauthorized to delete this message');
    }

    // Chatbot Management
    public function chatbotResponses()
    {
        $responses = ChatbotResponse::paginate(10);
        return view('admin.chatbot.responses', compact('responses'));
    }

    public function createChatbotResponse()
    {
        return view('admin.chatbot.create');
    }

    public function storeChatbotResponse(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string'
        ]);

        ChatbotResponse::create($validated);

        return redirect()->route('admin.chatbot.responses')->with('success', 'Response added successfully');
    }

    public function deleteChatbotResponse(ChatbotResponse $response)
    {
        $response->delete();
        return redirect()->route('admin.chatbot.responses')->with('success', 'Response deleted successfully');
    }
} 