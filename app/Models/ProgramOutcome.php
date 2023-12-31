<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramOutcome extends Model
{
    use HasFactory;
    protected $table = 'program_outcomes';
    protected $fillable = [
        'program_id','name','outcome_description'
    ];

    // If a relationship exists with the Program model
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function courseOutcomes()
    {
        return $this->belongsToMany(CourseOutcome::class, 'course_outcome_program_outcome')
            ->withPivot('strength') // Pivot column renamed to 'strength'
            ->withTimestamps();
    }
}
