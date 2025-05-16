@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
@endphp
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Chat Messages</div>

                <div class="card-body">
                    <div class="mb-3">
                        <strong>Logged in user ID:</strong> {{ $user->id ?? 'Guest' }}<br>
                        <strong>Logged in user role:</strong> {{ $user->role ?? 'Guest' }}
                    </div>

                    <form method="GET" action="{{ route('admin.chat.messages') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by sender, receiver or message" value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </form>

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Message</th>
                                    <th>Sent At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($messages as $message)
                                    <tr>
                                        <td>{{ $message->id }}</td>
                                        <td>{{ $message->sender->name }}</td>
                                        <td>{{ $message->receiver->name }}</td>
                                        <td>{{ Str::limit($message->content, 50) }}</td>
                                        <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @php
                                                $user = auth()->user();
                                            @endphp
                                            @if($user->role === 'admin' || $user->id === $message->sender->id)
                                                <form action="{{ route('admin.chat.messages.delete', $message) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this message?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 