@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add New Course</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Course Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">The course code will be automatically generated based on the course name.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Course Code</label>
                            <div class="form-control bg-light" id="generated_code">
                                Will be generated automatically
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="credits" class="form-label">Credits</label>
                            <input type="number" class="form-control @error('credits') is-invalid @enderror" id="credits" name="credits" value="{{ old('credits') }}" required>
                            @error('credits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="course_file" class="form-label">Course PDF File</label>
                            <input type="file" class="form-control @error('course_file') is-invalid @enderror" id="course_file" name="course_file" accept=".pdf">
                            @error('course_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload a PDF file (max 10MB)</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const codeDisplay = document.getElementById('generated_code');
    let typingTimer;

    nameInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        if (this.value) {
            typingTimer = setTimeout(function() {
                fetch('{{ route('courses.generate-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ name: nameInput.value })
                })
                .then(response => response.json())
                .then(data => {
                    codeDisplay.textContent = data.code;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }, 500);
        } else {
            codeDisplay.textContent = 'Will be generated automatically';
        }
    });
});
</script>
@endpush
@endsection 