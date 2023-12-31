<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOutcome extends Model
{
    use HasFactory;

    protected $table = 'course_outcomes';
    protected $fillable = [
        'course_id','name','outcome_description','status','max_marks_CIA','max_marks_ETA'
    ];
    public function program_outcomes()
    {
        return $this->belongsToMany(ProgramOutcome::class, 'course_outcome_program_outcome')
            ->withPivot('strength') // Rename the pivot column to 'strength'
            ->withTimestamps();
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function assessment_details()
    {
        return $this->hasMany(AssessmentDetail::class, 'course_outcome_id');
    }
}
