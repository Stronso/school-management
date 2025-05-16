<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'credits',
        'teacher_id',
        'course_file',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(\App\Models\CourseSubscription::class);
    }
} 