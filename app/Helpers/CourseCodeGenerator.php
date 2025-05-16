<?php

namespace App\Helpers;

use App\Models\Course;
use Illuminate\Support\Str;

class CourseCodeGenerator
{
    public static function generateCode($courseName)
    {
        // Get the first letters of each word in the course name
        $words = explode(' ', $courseName);
        $prefix = '';
        
        // Generate prefix from course name
        foreach ($words as $word) {
            if (strlen($prefix) < 4) { // Maximum 4 letters for prefix
                $prefix .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // If prefix is less than 2 letters, pad it
        while (strlen($prefix) < 2) {
            $prefix .= 'X';
        }

        // Find the latest number for this prefix
        $latestCourse = Course::where('code', 'LIKE', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        $number = 101; // Start with 101

        if ($latestCourse) {
            // Extract the number from the latest course code
            $lastNumber = (int) substr($latestCourse->code, strlen($prefix));
            $number = $lastNumber + 1;
            
            // If number is less than 101, set it to 101
            if ($number < 101) {
                $number = 101;
            }
        }

        // Format number to be 3 digits
        $numberStr = str_pad($number, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $numberStr;
    }
} 