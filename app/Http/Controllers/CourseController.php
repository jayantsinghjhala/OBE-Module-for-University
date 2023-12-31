<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Program;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{

    public function index()
    {
        $courses = Course::orderBy('created_at', 'desc')->get();
        $schools= School::all();
        return view('courses.index',[
            'courses' => $courses,
            'schools'=>$schools
        ]);

    }


    public function create()
    {
        $programs = Program::get();
        $schools= School::all();
        return view('courses.create', [
            'programs' => $programs,
            'schools'=>$schools
        ]);
    }

 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'course_credit' => 'required|integer',
            'type' => 'required|in:mandatory,elective',
            'program_id' => 'required|exists:programs,id',
            'school_id' => 'required|exists:schools,id', // Validate school_id
            'department_id' => 'required|exists:departments,id'
            // 'lab_credit' => 'required|integer',
            // 'short_description' => 'string',
            // 'minimal_requirement' => 'string',
            // 'study_material_summary' => 'string',
            // 'learning_media' => 'string',
        ]);

        $course = new Course();
        $course->code = $validated['code'];
        $course->name = $validated['name'];
        $course->course_credit = $validated['course_credit'];
        $course->type = $validated['type'];
        $course->program_id = $validated['program_id'];
        $course->creator_user_id = Auth::id();
        // $course->lab_credit = $validated['lab_credit'];
        // $course->short_description = $validated['short_description'];
        // $course->minimal_requirement = $validated['minimal_requirement'];
        // $course->study_material_summary = $validated['study_material_summary'];
        // $course->learning_media = $validated['learning_media'];

        $course->save();

        return redirect()->route('courses.index');
    }


    public function show(Course $course){
        return view('courses.show', [
            'course' => $course
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Course $course)
    {
        $current_school = $course->program->department->school->id;
        $current_department = $course->program->department->id;

        $schools= School::all();
        return view('courses.edit',[
            'course' => $course,
            'schools'=>$schools,
            'current_school'=>$current_school,
            'current_department'=>$current_department

        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'course_credit' => 'required|integer',
            'type' => 'required|in:mandatory,elective',
            'program_id' => 'required|exists:programs,id',
            'school_id' => 'required|exists:schools,id', // Validate school_id
            'department_id' => 'required|exists:departments,id'
            // 'lab_credit' => 'required|integer',
            // 'short_description' => 'string',
            // 'minimal_requirement' => 'string',
            // 'study_material_summary' => 'string',
            // 'learning_media' => 'string',
        ]);

        $course->code = $validated['code'];
        $course->name = $validated['name'];
        $course->course_credit = $validated['course_credit'];
        $course->type = $validated['type'];
        $course->program_id = $validated['program_id'];
        $course->creator_user_id = Auth::id();
        // $course->lab_credit = $validated['lab_credit'];
        // $course->short_description = $validated['short_description'];
        // $course->minimal_requirement = $validated['minimal_requirement'];
        // $course->study_material_summary = $validated['study_material_summary'];
        // $course->learning_media = $validated['learning_media'];
        $course->update();

        return redirect()->route('courses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index');
    }
}

