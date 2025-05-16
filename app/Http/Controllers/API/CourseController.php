<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('teacher');
        
        if ($request->user()->role === 'teacher') {
            $query->where('teacher_id', $request->user()->id);
        }
        
        if ($request->user()->role === 'student') {
            $query->where('status', 'published');
        }

        $courses = $query->latest()->paginate(10);
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'teacher') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file_path = $request->file('file')->store('courses');
        $thumbnail_path = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnail_path = $request->file('thumbnail')->store('course_thumbnails');
        }

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'teacher_id' => $request->user()->id,
            'file_path' => $file_path,
            'thumbnail' => $thumbnail_path,
            'status' => $request->status,
        ]);

        return response()->json($course, 201);
    }

    public function show(Course $course)
    {
        $course->load('teacher');
        return response()->json($course);
    }

    public function update(Request $request, Course $course)
    {
        if ($request->user()->role !== 'teacher' || $course->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('file')) {
            Storage::delete($course->file_path);
            $course->file_path = $request->file('file')->store('courses');
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::delete($course->thumbnail);
            }
            $course->thumbnail = $request->file('thumbnail')->store('course_thumbnails');
        }

        $course->fill($request->only(['title', 'description', 'status']));
        $course->save();

        return response()->json($course);
    }

    public function destroy(Request $request, Course $course)
    {
        if ($request->user()->role !== 'teacher' || $course->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::delete($course->file_path);
        if ($course->thumbnail) {
            Storage::delete($course->thumbnail);
        }

        $course->delete();
        return response()->json(['message' => 'Course deleted successfully']);
    }

    public function download(Request $request, Course $course)
    {
        if ($request->user()->role === 'student' && $course->status !== 'published') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return Storage::download($course->file_path);
    }
} 