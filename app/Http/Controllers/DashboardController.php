<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    
    public function index()
    {
        $user = auth()->user();

        if (Gate::allows('is-admin')) {
            $facultiesCount = User::where('role', 'teacher')->count();
            $departmentsCount = Department::count();
            $ProgramsCount = Program::count();
            // Retrieving courses, faculties, departments, and programs sorted by 'created_at' in descending order
            $courses = Course::orderBy('created_at', 'desc')->get();
            $faculties = User::where('role', 'teacher')->orderBy('created_at', 'desc')->get();
            $departments = Department::orderBy('created_at', 'desc')->get();
            $programs = Program::orderBy('created_at', 'desc')->get();

            return view('home', compact('user','facultiesCount','departmentsCount',
            'ProgramsCount','courses','faculties','departments','programs'));
        } else if (Gate::allows('is-teacher')) {
            $user = auth()->user(); // Get the authenticated user
            $assigned_courses = Course::whereHas('teachers', function ($query) use ($user) {
                $query->where('teacher_id', $user->id); // Adjust this condition based on your actual column names
            })->get();
        
            return view('home', compact('user', 'assigned_courses'));
        } else if (Gate::allows('is-student')) {
            $classes = CourseClass::whereHas('students', function ($query) use ($user) {
                $query->where('student_user_id', $user->id);
            })->get();

            return view('home', compact('user', 'classes'));
        }
    }
}
