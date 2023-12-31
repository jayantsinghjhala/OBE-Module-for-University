<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Course;
use App\Models\Program;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OfferCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::whereNotNull('semester')
                        ->where('semester', '>=', 1)
                        ->where('semester', '<=', 8)
                        ->get();

        $schools = School::all();

        return view('offer_courses.index', [
            'courses' => $courses,
            'schools' => $schools,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schools= School::all();
        return view('offer_courses.create',[
            'schools'=>$schools
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|in:1,2,3,4,5,6,7,8', // Validate semester value
        ]);

        // Find the course by its ID
        $course = Course::findOrFail($request->input('course_id'));

        // Update the course's semester field with the selected semester
        $course->update([
            'semester' => $request->input('semester'),
        ]);

        // You can add a success message or return a response as needed
        return redirect()->route('offer_courses.index')->with('success', 'Course offered successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $course = Course::findOrFail($id);
        return view('offer_courses.edit', ['course' => $course]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'new_semester' => 'required|integer|between:1,8', // Add any other validation rules you need
        ]);

        $course = Course::findOrFail($id);
        $course->update(['semester' => $request->input('new_semester')]);

        return redirect()->route('offer_courses.index')->with('success', 'Course semester updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the course by its ID
        $course = Course::findOrFail($id);

        // Update the 'semester' field to null
        $course->update(['semester' => null]);
        return redirect()->route('offer_courses.index')->with('success', 'Course offered successfully');
    }
 
    
}
