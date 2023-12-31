<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class, 'program_id', 'program_id')
            ->where(function ($query) {
                $query->where('semester', $this->semester);
            });
    }
    public function student_marks()
    {
        return $this->hasMany(StudentMark::class, 'student_id', 'id');
    }
}
