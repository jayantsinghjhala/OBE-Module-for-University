<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'program_id',
        'semester', // Add the 'semester' field to the fillable array
        'teacher_id'
    ];

    public function Program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'course_teachers', 'course_id', 'teacher_id')
            ->withPivot('role') // Define pivot columns
            ->withTimestamps();
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function course_outcomes()
    {
        return $this->hasMany(CourseOutcome::class, 'course_id');
    }

    public function courseClasses(){
        return $this->hasMany(CourseClass::class);
    }

    public function syllabi()
    {
        return $this->hasMany(Syllabus::class);
    }
    public function course_assessments()
    {
        return $this->hasMany(CourseAssessment::class, 'course_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'program_id', 'program_id')
            ->where('semester', $this->semester);
    }
}
