<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_name',
        'assessment_type', // Add other fields as needed
        'user_id' // Include the user_id field for mass assignment
    ];

    public function course_assessments()
    {
        return $this->hasMany(CourseAssessment::class, 'assessment_id');
    }

}