<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseSubscription;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class CourseSubscriptionController extends Controller
{
    /**
     * Subscribe the authenticated user to a course.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = Auth::user();

        $subscription = CourseSubscription::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
        ], [
            'approved' => false,
        ]);

        return response()->json([
            'message' => 'Subscribed successfully',
            'subscription' => $subscription,
        ]);
    }

    /**
     * Get subscribed courses with detailed info for the authenticated user.
     */
    public function getSubscribedCourses()
    {
        $user = Auth::user();

        $subscribedCourses = CourseSubscription::with(['course.teacher', 'course.students'])
            ->where('user_id', $user->id)
            ->where('approved', true)
            ->get()
            ->map(function ($subscription) {
                $course = $subscription->course;
                return [
                    'id' => $course->id,
                    'name' => $course->name ?? $course->title,
                    'code' => $course->code,
                    'credits' => $course->credits,
                    'description' => $course->description,
                    'teacher' => $course->teacher ? $course->teacher->name : null,
'course_file_url' => $course->course_file ? url('storage/' . $course->course_file) : null,
                    'enrolled_students_count' => $course->students->count(),
                ];
            });

        return response()->json($subscribedCourses);
    }

    /**
     * Check if the authenticated user is subscribed to a course.
     */
    public function checkSubscription(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = Auth::user();

        $subscription = CourseSubscription::where('user_id', $user->id)
            ->where('course_id', $request->course_id)
            ->first();

        return response()->json([
            'subscribed' => $subscription !== null,
            'approved' => $subscription ? $subscription->approved : false,
        ]);
    }

    /**
     * Get pending subscriptions for approval (for teachers/admins).
     */
    public function pendingSubscriptions()
    {
        $user = Auth::user();

        if (!$user->isTeacher() && !$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = CourseSubscription::where('approved', false);

        if ($user->isTeacher()) {
            $query = $query->whereHas('course', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }

        $pendingSubscriptions = $query->with(['user', 'course'])->get();

        return response()->json($pendingSubscriptions);
    }

    /**
     * Approve a subscription.
     */
    public function approveSubscription(Request $request, $subscriptionId)
    {
        $user = Auth::user();

        if (!$user->isTeacher() && !$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subscription = CourseSubscription::findOrFail($subscriptionId);

        if ($user->isTeacher() && $subscription->course->teacher_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subscription->approved = true;
        $subscription->save();

        // Redirect back to approval page with success message
        return redirect()->route('admin.course_subscriptions.approve')->with('success', 'Subscription approved successfully.');
    }

    /**
     * Show the approval page for web dashboard.
     */
    public function approvalPage()
    {
        $user = Auth::user();

        if (!$user->isTeacher() && !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = CourseSubscription::where('approved', false);

        if ($user->isTeacher()) {
            $query = $query->whereHas('course', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }

        $pendingSubscriptions = $query->with(['user', 'course'])->get();

        return view('course_subscriptions.approve', compact('pendingSubscriptions'));
    }

    /**
     * Show the student subscription page.
     */
    public function studentSubscribePage()
    {
        $courses = \App\Models\Course::with('teacher')->get();
        return view('course_subscriptions.student_subscribe', compact('courses'));
    }

    /**
     * Handle student subscription form submission.
     */
    public function subscribeStudent(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        $subscription = \App\Models\CourseSubscription::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
        ], [
            'approved' => false,
        ]);

        return redirect()->route('course_subscriptions.student_subscribe')->with('success', 'Subscription request submitted successfully.');
    }
}
