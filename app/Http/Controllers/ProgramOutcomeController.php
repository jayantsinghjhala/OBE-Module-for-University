<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Program;
use App\Models\School;
use App\Models\ProgramOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramOutcomeController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $program_outcomes = ProgramOutcome::orderBy('created_at', 'desc')->get();
        $schools = School::all();
        return view('program_outcomes.index', [
            'program_outcomes' => $program_outcomes,
            'schools'=> $schools
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schools= School::all();
        return view('program_outcomes.create',[
            'schools'=>$schools
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|integer',
            'name' => 'required|string',
            'outcome_description' => 'required|string',
        ]);
    
        // Check if the combination of program_id and name already exists
        $existingOutcome = ProgramOutcome::where('program_id', $validated['program_id'])
            ->where('name', $validated['name'])
            ->first();
    
        if ($existingOutcome) {
            return redirect()->back()->withInput()->withErrors([
                'name' => 'This outcome name already exists for the selected program.'
            ]);
        }
    
        $program_outcomes = new ProgramOutcome();
        $program_outcomes->program_id = $validated['program_id'];
        $program_outcomes->name = $validated['name'];
        $program_outcomes->outcome_description = $validated['outcome_description'];
        $program_outcomes->save();
    
        // Redirect to the index view with a success message
        return redirect()->route('program_outcomes.index')->with('success', 'Program Outcome created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgramOutcome $program_outcome)
    {
        // Retrieve the program associated with the student
        $program = Program::find($program_outcome->program_id);

        if (!$program) {
            // Handle the case where the program is not found
            return redirect()->route('student.index')->with('error', 'Program not found');
        }

        // Extract the school and department information from the program
        $current_school = $program->department->school->id;
        $current_department = $program->department->id;

        // Retrieve all schools
        $schools = School::all();

        return view('program_outcomes.edit', [
            'program_outcome' => $program_outcome,
            'schools' => $schools,
            'current_school' => $current_school,
            'current_department' => $current_department
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'program_id' => 'required|integer',
            'name' => 'required|string',
            'outcome_description' => 'required|string',
        ]);
    
        // Check if the combination of program_id and name already exists
        $program_outcomes = ProgramOutcome::findOrFail($id);
        if ($program_outcomes->name !== $validated['name']) {
            // If the name has changed, check if the combination of course_id and name already exists
            $existingOutcome = ProgramOutcome::where('program_id', $validated['program_id'])
                ->where('name', $validated['name'])
                ->first();
        
            if ($existingOutcome) {
                return redirect()->back()->withInput()->withErrors([
                    'name' => 'This outcome name already exists for the selected program.'
                ]);
            }
        }

        

        $program_outcomes->update([
            'program_id' => $validated['program_id'],
            'name' => $validated['name'],
            'outcome_description' => $validated['outcome_description'],
        ]);

        return redirect()->route('program_outcomes.index')->with('success', 'Program Outcome updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $program_outcomes = ProgramOutcome::findOrFail($id);
        $program_outcomes->delete();

        return redirect()->route('program_outcomes.index')->with('success', 'Program Outcome deleted successfully');
    }
    
}
