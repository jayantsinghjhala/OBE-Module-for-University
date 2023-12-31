<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_assessment_id',
        'course_outcome_id',
        'question_number',
        'marks',
        // Add other fillable attributes here as needed
    ];

    // Define relationships
    public function course_assessment()
    {
        return $this->belongsTo(CourseAssessment::class);
    }

    public function course_outcome()
    {
        return $this->belongsTo(CourseOutcome::class);
    }

    public function student_marks()
    {
        return $this->hasMany(StudentMark::class, 'assessment_detail_id', 'id');
    }
}
