<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $activities = $course->activities()
            ->when($request->user()->role === 'student', function ($query) {
                return $query->whereHas('course', function ($q) {
                    $q->where('status', 'published');
                });
            })
            ->with(['submissions' => function ($query) use ($request) {
                $query->where('student_id', $request->user()->id);
            }])
            ->latest()
            ->get();

        return response()->json($activities);
    }

    public function store(Request $request, Course $course)
    {
        if ($request->user()->role !== 'teacher' || $course->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:now',
            'max_score' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $activity = $course->activities()->create($request->all());
        return response()->json($activity, 201);
    }

    public function show(Activity $activity)
    {
        $activity->load(['course', 'submissions' => function ($query) {
            $query->with('student');
        }]);
        return response()->json($activity);
    }

    public function update(Request $request, Activity $activity)
    {
        if ($request->user()->role !== 'teacher' || $activity->course->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'due_date' => 'date|after:now',
            'max_score' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $activity->update($request->all());
        return response()->json($activity);
    }

    public function destroy(Request $request, Activity $activity)
    {
        if ($request->user()->role !== 'teacher' || $activity->course->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        foreach ($activity->submissions as $submission) {
            Storage::delete($submission->file_path);
        }

        $activity->delete();
        return response()->json(['message' => 'Activity deleted successfully']);
    }

    public function submit(Request $request, Activity $activity)
    {
        if ($request->user()->role !== 'student') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file_path = $request->file('file')->store('submissions');

        $submission = ActivitySubmission::updateOrCreate(
            [
                'activity_id' => $activity->id,
                'student_id' => $request->user()->id,
            ],
            ['file_path' => $file_path]
        );

        return response()->json($submission, 201);
    }

    public function grade(Request $request, ActivitySubmission $submission)
    {
        if ($request->user()->role !== 'teacher' || $submission->activity->course->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'score' => 'required|integer|min:0|max:' . $submission->activity->max_score,
            'feedback' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $submission->update($request->only(['score', 'feedback']));
        return response()->json($submission);
    }

    public function downloadSubmission(Request $request, ActivitySubmission $submission)
    {
        if ($request->user()->role === 'student' && $submission->student_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($request->user()->role === 'teacher' && $submission->activity->course->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return Storage::download($submission->file_path);
    }
} 