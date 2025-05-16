@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $course->name }}</h4>
                    <div>
                        <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        @if($course->teacher_id === Auth::id() || Auth::user()->isAdmin())
                            <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
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

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Course Code</h6>
                            <p>{{ $course->code }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Credits</h6>
                            <p>{{ $course->credits }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Description</h6>
                        <p>{{ $course->description }}</p>
                    </div>

                    <div class="mb-4">
                        <h6>Teacher</h6>
                        <p>{{ $course->teacher->name }}</p>
                    </div>

                    @if($course->course_file)
                        <div class="mb-4">
                            <h6>Course Material</h6>
                            <a href="{{ route('courses.download', $course) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    @endif

                    @if($course->teacher_id === Auth::id() || Auth::user()->isAdmin())
                        <div class="mb-4">
                            <h6>Enrolled Students</h6>
                            @if($course->students->count() > 0)
                                <ul class="list-group">
                                    @foreach($course->students as $student)
                                        <li class="list-group-item">{{ $student->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No students enrolled yet.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 