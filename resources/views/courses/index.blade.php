@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col">
            <h1>{{ Auth::user()->isStudent() ? 'Available Courses' : 'My Courses' }}</h1>
        </div>
        <div class="col-auto">
            @if(Auth::user()->isTeacher() || Auth::user()->isAdmin())
                <a href="{{ route('courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Course
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('courses.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by course name or teacher" value="{{ request('search', $search ?? '') }}">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        @forelse($courses as $course)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('courses.show', $course) }}" class="text-decoration-none">
                                {{ $course->name }}
                            </a>
                        </h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $course->code }}</h6>
                        <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                        <p class="card-text">
                            <small class="text-muted">
                                Credits: {{ $course->credits }} | 
                                Teacher: {{ $course->teacher->name }}
                            </small>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        @if(Auth::user()->isStudent() && $course->course_file)
                            <a href="{{ route('courses.download', $course) }}" class="btn btn-success btn-sm" download>
                                <i class="fas fa-download"></i> Download
                            </a>
                        @endif
                        @if(Auth::user()->isAdmin() || Auth::user()->id === $course->teacher_id)
                            <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    @if(Auth::user()->isStudent())
                        No courses available at the moment.
                    @else
                        No courses found. <a href="{{ route('courses.create') }}">Create your first course</a>.
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection 