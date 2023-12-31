<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Course;
use App\Models\School; 
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


class ProgramController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Program::class, 'program');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */

    public function index()
    {
        $programs = Program::orderBy('created_at', 'desc')->get();
        $schools = School::all();
        return view('programs.index', [
            'programs' => $programs,
            'schools'=>$schools
        ]);
    }

    public function show(Faculty $faculty, Department $department, Program $Program){
        abort(404);
    }

    public function create()
    {
        $schools = School::all();
        return view('programs.create', [
            'schools' => $schools,
        ]);
    }

    public function get_departments($school_id)
    {
        try {
            $departments = Department::where('school_id', $school_id)->get();
            $response = [
                'status' => 1,
                'departments' => $departments,
            ];
            $response_code = 200; 
        } catch (\Exception $e) {
            $response = [
                'status' => 0,
                'message' => 'Error fetching departments',
            ];
            $response_code = 500; 
        }
    
        return response()->json($response, $response_code);
    }
    public function get_programs($department_id)
    {
        try {
            $programs = Program::where('department_id', $department_id)->get();
            $response = [
                'status' => 1,
                'programs' => $programs,
            ];
            $response_code = 200; 
        } catch (\Exception $e) {
            $response = [
                'status' => 0,
                'message' => 'Error fetching programs',
            ];
            $response_code = 500; 
        }
    
        return response()->json($response, $response_code);
    }
    
    public function get_courses($program_id, $semester = null)
    {
        try {
            $query = Course::where('program_id', $program_id);
    
            if ($semester !== null) {
                // If a semester is provided, add a filter based on the semester column
                $query->where('semester', $semester);
            }
    
            $courses = $query->get();
    
            $response = [
                'status' => 1,
                'courses' => $courses,
            ];
            $response_code = 200;
        } catch (\Exception $e) {
            $response = [
                'status' => 0,
                'message' => 'Error fetching programs',
            ];
            $response_code = 500;
        }
    
        return response()->json($response, $response_code);
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
            'name' => 'required|string|min:3',
        ]);
    
        $program = new Program();                  
        $program->department_id = $validated['department_id'];
        $program->name = $validated['name'];
        $program->save();
    
        return redirect()->route('programs.index');
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param Faculty $faculty
     * @param Department $department
     * @param Program $Program
     * @return Application|Factory|View
     */
    public function edit(Program $program)
    {
        // Get the school based on the department of the program
        $current_school = $program->department->school->id;
        $schools=School::all();
        return view('programs.edit', [
            'schools'=>$schools,
            'current_school' => $current_school,
            'program' => $program,
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Faculty $faculty
     * @param Department $department
     * @param Program $Program
     * @return RedirectResponse
     */
    public function update(Request $request,Department $department, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
            'name' => 'required|string'
        ]);

        $program->update($validated);


        return redirect()->route('programs.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Faculty $faculty
     * @param Department $department
     * @param Program $Program
     * @return RedirectResponse
     */
    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('programs.index');
    }
}
