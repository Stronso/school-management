@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Course: {{ $course->name }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Course Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $course->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">Course Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                id="code" name="code" value="{{ old('code', $course->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3" required>{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="credits" class="form-label">Credits</label>
                            <input type="number" class="form-control @error('credits') is-invalid @enderror" 
                                id="credits" name="credits" value="{{ old('credits', $course->credits) }}" required>
                            @error('credits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="course_file" class="form-label">Course PDF File</label>
                            <input type="file" class="form-control @error('course_file') is-invalid @enderror" 
                                id="course_file" name="course_file" accept=".pdf">
                            @error('course_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($course->course_file)
                                <div class="mt-2">
                                    <p class="mb-1">Current file: 
                                        <a href="{{ route('courses.download', $course) }}" target="_blank">
                                            Download PDF
                                        </a>
                                    </p>
                                    <small class="text-muted">Upload a new file to replace the current one</small>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 