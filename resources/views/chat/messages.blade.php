@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Chat</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(isset($users))
        <form method="GET" action="{{ route('chat.messages') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search users by name or email" value="{{ $search ?? '' }}">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>

        <ul class="list-group" style="max-height: 400px; overflow-y: auto;">
            @foreach($users as $user)
                <li class="list-group-item d-flex align-items-center">
                    <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="{{ $user->name }}" class="rounded-circle me-3" width="40" height="40">
                    <a href="{{ route('chat.messages', ['conversationId' => $user->id]) }}">{{ $user->name }} ({{ $user->email }})</a>
                </li>
            @endforeach
        </ul>
    @elseif(isset($conversation))
        <div class="d-flex align-items-center mb-3">
            <img src="{{ $conversation->users->firstWhere('id', '!=', auth()->id())->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($conversation->users->firstWhere('id', '!=', auth()->id())->name) }}" alt="{{ $conversation->users->firstWhere('id', '!=', auth()->id())->name }}" class="rounded-circle me-3" width="50" height="50">
            <h2 class="mb-0">Conversation with {{ $conversation->users->firstWhere('id', '!=', auth()->id())->name }}</h2>
        </div>
        <div class="chat-container" style="max-width: 600px; margin: auto; height: 400px; overflow-y: auto;">
            @foreach($messages as $message)
                <div class="chat-message d-flex {{ $message->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}" style="margin-bottom: 15px;">
                    @if($message->sender_id !== auth()->id())
                        <img src="{{ $message->sender->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional($message->sender)->name) }}" alt="{{ optional($message->sender)->name ?? 'Unknown' }}" class="rounded-circle me-2" width="40" height="40">
                    @endif
                    <div style="background-color: {{ $message->sender_id === auth()->id() ? '#DCF8C6' : '#FFF' }}; padding: 10px; border-radius: 10px; max-width: 80%; box-shadow: 0 1px 1px rgba(0,0,0,0.1);">
                        <p style="margin: 0;">{{ $message->content }}</p>
                        @if($message->attachment_path)
                            <p style="margin: 5px 0 0 0;">
                                Attachment: <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank">View File</a>
                            </p>
                        @endif
                    </div>
                    @if($message->sender_id === auth()->id())
                        <img src="{{ $message->sender->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional($message->sender)->name) }}" alt="{{ optional($message->sender)->name ?? 'Unknown' }}" class="rounded-circle ms-2" width="40" height="40">
                    @endif
                </div>
                <small style="color: #999; margin-top: 3px; display: block; text-align: {{ $message->sender_id === auth()->id() ? 'right' : 'left' }};">
                    {{ $message->created_at->format('Y-m-d H:i') }} - 
                    @if($message->sender_id === auth()->id())
                        Sent to: {{ optional($message->receiver)->name ?? 'Unknown' }}
                    @else
                        Received from: {{ optional($message->sender)->name ?? 'Unknown' }}
                    @endif
                </small>
                @if($message->sender_id === auth()->id())
                    <form action="{{ route('chat.messages.delete', $message->id) }}" method="POST" style="margin-top: 5px; text-align: {{ $message->sender_id === auth()->id() ? 'right' : 'left' }};">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this message?')">Delete</button>
                    </form>
                @endif
            @endforeach
        </div>

        <form action="{{ route('chat.messages.store') }}" method="POST" enctype="multipart/form-data" style="max-width: 600px; margin: auto; margin-top: 15px;">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $conversation->users->firstWhere('id', '!=', auth()->id())->id }}">

            <div class="mb-3">
                <label for="content" class="form-label">New Message</label>
                <textarea name="content" id="content" rows="3" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label for="attachment" class="form-label">Attachment (optional)</label>
                <input type="file" name="attachment" id="attachment" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    @else
        <p>No conversations or users to display.</p>
    @endif
</div>
@endsection
