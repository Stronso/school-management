@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Select User to Chat</h1>

    <input type="text" id="userSearch" class="form-control mb-3" placeholder="Search users by name or email">

    <ul class="list-group" id="userList" style="max-height: 400px; overflow-y: auto;">
        @foreach($users as $user)
            <li class="list-group-item list-group-item-action user-item" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                <a href="{{ route('chat.messages', ['conversationId' => $user->id]) }}" class="stretched-link">{{ $user->name }} ({{ $user->email }})</a>
            </li>
        @endforeach
    </ul>
</div>

<script>
    document.getElementById('userSearch').addEventListener('input', function() {
        var filter = this.value.toLowerCase();
        var items = document.querySelectorAll('#userList .user-item');
        items.forEach(function(item) {
            var name = item.getAttribute('data-name');
            var email = item.getAttribute('data-email');
            if (name.includes(filter) || email.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection
