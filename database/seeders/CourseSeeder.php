<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Get a teacher user
        $teacher = User::where('role', 'teacher')->first();

        // Sample courses with proper credits
        $courses = [
            [
                'name' => 'Introduction to Mathematics',
                'description' => 'Basic concepts of mathematics including algebra, geometry, and trigonometry.',
                // Using default credits (3)
                'teacher_id' => $teacher->id,
            ],
            [
                'name' => 'Computer Programming Fundamentals',
                'description' => 'Introduction to programming concepts and problem-solving techniques.',
                'credits' => 4, // Specified credits for Lecture + Lab
                'teacher_id' => $teacher->id,
            ],
            [
                'name' => 'Physics Lab',
                'description' => 'Hands-on experiments in basic physics concepts.',
                'credits' => null, // No credits specified
                'teacher_id' => $teacher->id,
            ],
            [
                'name' => 'Advanced Calculus',
                'description' => 'In-depth study of calculus including derivatives and integrals.',
                'credits' => 5, // Advanced course with extra hours
                'teacher_id' => $teacher->id,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = new Course($courseData);
            $course->code = \App\Helpers\CourseCodeGenerator::generateCode($courseData['name']);
            $course->save();
        }
    }
} 