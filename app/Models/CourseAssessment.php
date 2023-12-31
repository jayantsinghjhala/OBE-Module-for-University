<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAssessment extends Model
{
    use HasFactory;

    protected $table = 'course_assessments'; // Specify the table name explicitly

    protected $fillable = [
        'course_id',
        'assessment_id',
        'num_questions',
        'assessment_date',
        'maximum_marks'
        // Add other columns that are fillable
    ];

    // Define relationships
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function assessment_details()
    {
        return $this->hasMany(AssessmentDetail::class, 'course_assessment_id');
    }

    // You can define more relationships or methods as needed
}
