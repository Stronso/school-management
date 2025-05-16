<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Helpers\CourseCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        if ($user->isAdmin()) {
            $query = Course::with('teacher');
        } elseif ($user->isTeacher()) {
            $query = $user->courses()->with('teacher');
        } else {
            // For students, get only courses they subscribed to and approved
            $user = Auth::user();
            $query = Course::whereHas('subscriptions', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('approved', true);
            })->with('teacher');
        }

        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('teacher', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $courses = $query->get();

        return view('courses.index', compact('courses', 'search'));
    }

    public function create()
    {
        if (!Auth::user()->isTeacher() && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.index')
                ->with('error', 'Only teachers and administrators can create courses.');
        }

        return view('courses.create');
    }

    public function generateCode(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $code = CourseCodeGenerator::generateCode($request->name);
        return response()->json(['code' => $code]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isTeacher() && !Auth::user()->isAdmin()) {
            return redirect()->route('courses.index')
                ->with('error', 'Only teachers and administrators can create courses.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'credits' => 'required|integer|min:1',
            'course_file' => 'nullable|mimes:pdf|max:10240', // Max 10MB PDF file
        ]);

        // Generate course code
        $validated['code'] = CourseCodeGenerator::generateCode($validated['name']);

        $course = new Course($validated);
        $course->teacher_id = Auth::id();

        if ($request->hasFile('course_file')) {
            $file = $request->file('course_file');
            $path = $file->store('course-files', 'public');
            $course->course_file = $path;
        }

        $course->save();

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load(['teacher', 'students']);
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        if (!Auth::user()->isAdmin() && Auth::id() !== $course->teacher_id) {
            return redirect()->route('courses.index')
                ->with('error', 'You are not authorized to edit this course.');
        }

        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        if (!Auth::user()->isAdmin() && Auth::id() !== $course->teacher_id) {
            return redirect()->route('courses.index')
                ->with('error', 'You are not authorized to edit this course.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'credits' => 'required|integer|min:1',
            'course_file' => 'nullable|mimes:pdf|max:10240', // Max 10MB PDF file
        ]);

        // Keep the existing code
        $validated['code'] = $course->code;

        if ($request->hasFile('course_file')) {
            // Delete old file if exists
            if ($course->course_file) {
                Storage::disk('public')->delete($course->course_file);
            }

            $file = $request->file('course_file');
            $path = $file->store('course-files', 'public');
            $validated['course_file'] = $path;
        }

        $course->update($validated);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() && $course->teacher_id !== $user->id) {
            return redirect()->route('courses.index')
                ->with('error', 'You are not authorized to delete this course.');
        }

        // Delete course file if exists
        if ($course->course_file) {
            Storage::disk('public')->delete($course->course_file);
        }

        $course->delete();
        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function downloadFile(Course $course)
    {
        if (!$course->course_file) {
            return redirect()->back()->with('error', 'No file available for this course.');
        }

        return Storage::disk('public')->download($course->course_file);
    }
} 