@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pending Course Subscription Approvals</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($pendingSubscriptions->isEmpty())
        <div class="alert alert-info">No pending subscriptions for approval.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingSubscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->user->name }}</td>
                    <td>{{ $subscription->course->name }}</td>
                    <td>{{ $subscription->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <form action="{{ route('admin.course_subscriptions.approve.post', $subscription->id) }}" method="POST" onsubmit="return confirm('Approve this subscription?');">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
