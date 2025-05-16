@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Conversations</h1>

    @if($conversations->isEmpty())
        <p>You have no conversations.</p>
    @else
        <div class="list-group">
            @foreach($conversations as $conversation)
                @php
                    $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                    $lastMessage = $conversation->messages->first();
                @endphp
                <a href="{{ route('chat.messages', ['conversationId' => $conversation->id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $otherUser->name ?? 'Unknown User' }}</strong><br>
                        @if($lastMessage)
                            <small class="text-muted">{{ Str::limit($lastMessage->content, 50) }}</small>
                        @else
                            <small class="text-muted">No messages yet</small>
                        @endif
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $lastMessage ? $lastMessage->created_at->format('Y-m-d H:i') : '' }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
