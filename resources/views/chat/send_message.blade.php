@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Send a Message</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('chat.messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="receiver_id" class="form-label">Select User to Message</label>
            <select name="receiver_id" id="receiver_id" class="form-select" required>
                <option value="" disabled>Choose a user</option>
                <option value="all">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Message</label>
            <textarea name="content" id="content" rows="4" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="attachment" class="form-label">Attachment (optional)</label>
            <input type="file" name="attachment" id="attachment" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>
@endsection
