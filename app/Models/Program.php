<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function program_outcomes()
    {
        return $this->hasMany(ProgramOutcome::class);
    }
    protected $fillable = [
        'department_id','name'
    ];
}
