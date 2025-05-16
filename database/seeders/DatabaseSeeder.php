<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Activity;
use App\Models\ActivitySubmission;
use Illuminate\Support\Facades\Hash;
use App\Helpers\CourseCodeGenerator;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CourseSeeder::class,
            ChatTestSeeder::class,
        ]);

        // Create Teacher
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        // Create Student
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@school.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Create Course
        $course = Course::create([
            'name' => 'Introduction to Programming',
            'description' => 'Learn the basics of programming with this comprehensive course',
            'teacher_id' => $teacher->id,
            'status' => 'published',
            'code' => CourseCodeGenerator::generateCode('Introduction to Programming'),
        ]);

        // Create Activity
        $activity = Activity::create([
            'title' => 'First Assignment',
            'description' => 'Create a simple Hello World program',
            'course_id' => $course->id,
            'due_date' => now()->addDays(7),
            'max_score' => 100,
        ]);

        // Create Activity Submission
        ActivitySubmission::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'file_path' => 'submissions/test.txt',
            'score' => null,
            'feedback' => null,
        ]);
    }
}
