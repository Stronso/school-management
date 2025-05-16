@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Subscribe to Courses</h1>

    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search courses...">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($courses->isEmpty())
        <div class="alert alert-info">No courses available for subscription.</div>
    @else
        <table class="table table-bordered" id="coursesTable">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Created By</th>
                    <th>Subscribe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->description }}</td>
                    <td>{{ $course->teacher ? $course->teacher->name : 'N/A' }}</td>
                    <td>
                        <form action="{{ route('course_subscriptions.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            <button type="submit" class="btn btn-primary">Subscribe</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('coursesTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();

                for (let i = 0; i < rows.length; i++) {
                    const courseName = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                    const description = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
                    const teacherName = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();

                    if (courseName.indexOf(filter) > -1 || description.indexOf(filter) > -1 || teacherName.indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            });
});
</script>
@endsection
