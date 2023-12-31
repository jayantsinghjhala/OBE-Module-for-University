<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail,JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    public function getJWTCustomClaims() {
        return [];
    }
    public function joinedClasses()
    {
        return $this->belongsToMany(CourseClass::class, 'join_classes', 'student_user_id',
            'course_class_id');
    }

    public function createdClasses()
    {
        return $this->hasMany(CourseClass::class, 'creator_user_id');
    }

    public function studentGrades()
    {
        return $this->hasMany(StudentGrade::class, 'student_user_id');
    }

    public function studentData()
    {
        return $this->hasOne(StudentData::class, 'id');
    }

    public function courseClass()
    {
        return $this->belongsToMany(
            CourseClass::class,
            'join_classes',
            'student_user_id',
            'course_class_id'
        );
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_teachers', 'teacher_id', 'course_id')
            ->withPivot('role'); // Include pivot columns if needed
    }
}
