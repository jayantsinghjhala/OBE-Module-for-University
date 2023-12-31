<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

class StudentController extends Controller
{
    public function index()
    {
        $schools= School::all();
        $students = Student::orderBy('created_at', 'desc')->get();
        $programs = Program::all()->keyBy('id');

        return view('students.index', [
            'students' => $students,
            'schools'=>$schools,
            'programs'=>$programs
        ]);
    }



    public function create()
    {
        $schools = School::all(); // Retrieve schools for the create form
        return view('students.create', ['schools' => $schools]);
    }

  
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'enrollment_number' => 'required|string|unique:students',
            'student_name' => 'required|string',
            'start_year' => 'required|string',
            'end_year' => 'required|string',
            'program_id' => 'required|exists:programs,id',
            'semester' => 'required|string|between:1,8', // Assuming semesters are from 1 to 8
        ]);

        $student = new Student();                  
        $student->enrollment_number = $validated['enrollment_number'];
        $student->student_name = $validated['student_name'];
        $student->session = $validated['start_year']."-".$validated['end_year'];
        $student->program_id = $validated['program_id'];
        $student->semester = $validated['semester'];
        $student->save();

        return redirect()->route('students.index');
    }

    public function show(Student $student)
    {
        return view('students.show', [
            'student' => $student
        ]);
    }

    public function edit(Student $student)
    {
        // Retrieve the program associated with the student
        $program = Program::find($student->program_id);

        if (!$program) {
            // Handle the case where the program is not found
            return redirect()->route('student.index')->with('error', 'Program not found');
        }

        // Extract the school and department information from the program
        $current_school = $program->department->school->id;
        $current_department = $program->department->id;

        // Retrieve all schools
        $schools = School::all();

        return view('students.edit', [
            'student' => $student,
            'schools' => $schools,
            'current_school' => $current_school,
            'current_department' => $current_department
        ]);
    }


    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'enrollment_number' => 'required|string|unique:students,enrollment_number,' . $student->id,
            'student_name' => 'required|string',
            'start_year' => 'required|string',
            'end_year' => 'required|string',
            'program_id' => 'required|exists:programs,id',
            'semester' => 'required|integer|between:1,8',
        ]);
        
        $student->enrollment_number = $validated['enrollment_number'];
        $student->student_name = $validated['student_name'];
        $student->session = $validated['start_year']."-".$validated['end_year'];
        $student->program_id = $validated['program_id'];
        $student->semester = $validated['semester'];
        $student->save();

        return redirect(route('students.index'));
    }

    public function destroy(Student $student)
    {
        // Delete the student
        $student->delete();

        return redirect(route('students.index'));
    }
}
