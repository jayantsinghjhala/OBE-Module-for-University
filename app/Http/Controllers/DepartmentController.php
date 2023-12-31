<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;



class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Department::class, 'department');
    }

    public function index()
    {
        $departments = Department::orderBy('created_at', 'desc')->get();//->paginate(10);
        $schools = School::all(); // Load all schools
        return view('departments.index', compact('departments', 'schools'));
    }
    

    public function create()
    {
        $schools = School::all();
        return view('departments.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|unique:departments,name',
            'school_id' => 'required|exists:schools,id',
        ]);

        $department = Department::create($validated);

        return redirect()->route('departments.index');
    }

    public function show(Department $department){
        abort(404);
    }

    public function edit(Department $department)
    {
        $schools = School::all();
        return view('departments.edit', compact('department', 'schools'));
    }


    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('departments', 'name')->ignore($department->id),
            ],
            'school_id' => 'required|exists:schools,id',
        ]);
    
        $department->update($validated);
    
        return redirect()->route('departments.index');
    }

    public function destroy(Department $department)
    {
        // Check if the user is authorized to delete the department
        if (Gate::allows('delete', $department)) {
            $department->delete();
            return redirect()->route('departments.index');
        }

        return abort(403, 'Unauthorized');
    }
}

