<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOutcomeProgramOutcome extends Model
{
    use HasFactory;

    protected $table = 'course_outcome_program_outcome'; // Set the table name

    protected $primaryKey = 'id'; // Set the primary key name

    protected $fillable = [
        'course_outcome_id',
        'program_outcome_id',
        'strength',
        // Add other fillable fields if any
    ];

    // Define relationships if needed
    // For example, belongsTo CourseOutcome and ProgramOutcome
    public function courseOutcome()
    {
        return $this->belongsTo(CourseOutcome::class, 'course_outcome_id');
    }

    public function programOutcome()
    {
        return $this->belongsTo(ProgramOutcome::class, 'program_outcome_id');
    }
}
