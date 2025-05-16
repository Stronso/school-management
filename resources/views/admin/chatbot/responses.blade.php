@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chatbot Responses</h5>
                    <a href="{{ route('admin.chatbot.responses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Response
                    </a>
                </div>

                <div class="card-body">
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
                                    <th>Question</th>
                                    <th>Answer</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($responses as $response)
                                    <tr>
                                        <td>{{ $response->id }}</td>
                                        <td>{{ Str::limit($response->question, 50) }}</td>
                                        <td>{{ Str::limit($response->answer, 100) }}</td>
                                        <td>{{ $response->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <form action="{{ route('admin.chatbot.responses.delete', $response) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this response?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $responses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 