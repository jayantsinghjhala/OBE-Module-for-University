<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_detail_id',
        'student_id',
        'obtained_marks',
        // Add other fillable attributes here as needed
    ];

    // Define relationships
    public function assessment_detail()
    {
        return $this->belongsTo(AssessmentDetail::class, 'assessment_detail_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
