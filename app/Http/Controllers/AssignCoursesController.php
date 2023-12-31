<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Course;
use App\Models\Program;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use App\Models\TeacherSubjectMapping; 


class AssignCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $primaryCourses = Course::whereNotNull('semester')
            ->where('semester', '>=', 1)
            ->where('semester', '<=', 8)
            ->whereHas('teachers', function ($query) {
                $query->where('course_teachers.role', 'primary');
            })
            ->with('teachers')
            ->get();
    
        $secondaryCourses = Course::whereNotNull('semester')
            ->where('semester', '>=', 1)
            ->where('semester', '<=', 8)
            ->whereHas('teachers', function ($query) {
                $query->where('course_teachers.role', 'secondary');
            })
            ->with('teachers')
            ->get();
    
        $schools = School::all();
        $teachers = User::where('role', 'teacher')->get();
    
        return view('assign_courses.index', [
            'primaryCourses' => $primaryCourses,
            'secondaryCourses' => $secondaryCourses,
            'schools' => $schools,
            'teachers' => $teachers
        ]);
    }
    


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schools = School::all();
        $teachers = User::where('role', 'teacher')->get(); // Fetch teachers

        return view('assign_courses.create', [
            'schools' => $schools,
            'teachers' => $teachers,
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
             'teacher_id' => 'required|exists:users,id', // Validate faculty (teacher) ID
             'role' => 'required|in:primary,secondary' // Validate role as primary or secondary
         ]);
     
         $course = Course::findOrFail($request->input('course_id'));
         $teacher = User::findOrFail($request->input('teacher_id'));
         $role = $request->input('role');
     
         // Check if the selected teacher is already assigned as a primary or secondary teacher to the same subject
         $existingAssignment = $course->teachers()
            ->wherePivot('teacher_id', '=', $teacher->id)
            ->whereIn('course_teachers.role', ['primary', 'secondary'])
            ->exists();
            
         if ($existingAssignment) {
             return redirect()->route('assign_courses.create')->withErrors(['teacher_id' => 'Selected teacher is already assigned to this course as a Primary or Secondary teacher.']);
         }
     
         if ($role === 'primary') {
             // Check if the primary role is already assigned to any other teacher for this course
             $primaryTeacherAssigned = $course->teachers()->wherePivot('role', 'primary')->exists();
     
             if ($primaryTeacherAssigned) {
                 return redirect()->route('assign_courses.create')->withErrors(['role' => 'Primary role is already assigned to a teacher for this course.']);
             }
         } elseif ($role === 'secondary') {
             // Count the number of secondary teachers assigned to the course
             $secondaryTeachersCount = $course->teachers()->wherePivot('role', 'secondary')->count();
     
             if ($secondaryTeachersCount >= 5) { // Assuming maximum 5 secondary teachers allowed
                 return redirect()->route('assign_courses.create')->withErrors(['role' => 'Maximum limit for Secondary teachers reached.']);
             }
         }
     
         // Assign the teacher to the course with the specified role
         $course->teachers()->attach($teacher, ['role' => $role]);
     
         // Return a success message or redirect to index page
         return redirect()->route('assign_courses.index')->with('success', 'Course assigned successfully');
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
    public function edit_primary(string $id)
    {
        $course = Course::findOrFail($id);
        // Get the primary teacher for this course
        $current_teacher = $course->teachers()->wherePivot('role', 'primary')->first();
        $teachers = User::where('role', 'teacher')->get();
        
        // Determine if it's a primary edit (true) or secondary edit (false)
        $isPrimary = true;

        return view('assign_courses.edit', [
            'course' => $course,
            'current_teacher' => $current_teacher,
            'teachers' => $teachers,
            'isPrimary' => $isPrimary, // Pass $isPrimary to the view
        ]);
    }
    
    public function update_primary(Request $request, string $id)
    {
        $request->validate([
            'new_teacher_id' => 'required|exists:users,id', // Add any other validation rules you need
        ]);

        $course = Course::findOrFail($id);

        // Check if the selected teacher is already assigned to the course as a secondary teacher
        $isSecondaryTeacher = $course->teachers()
            ->wherePivot('teacher_id', $request->input('new_teacher_id'))
            ->wherePivot('role', 'secondary')
            ->exists();

        if ($isSecondaryTeacher) {
            return redirect()->route('assign_courses.edit_primary', $id)->withErrors(['new_teacher_id' => 'Selected teacher is already assigned as Secondary to this course.']);
        }

        // Find the primary teacher pivot record
        $primaryTeacherPivot = $course->teachers()->wherePivot('role', 'primary')->first();

        if ($primaryTeacherPivot) {
            // Update the teacher_id for the primary role
            $primaryTeacherPivot->pivot->update(['teacher_id' => $request->input('new_teacher_id')]);

            return redirect()->route('assign_courses.index')->with('success', 'Primary teacher updated successfully.');
        }

        return redirect()->route('assign_courses.index')->with('error', 'No primary teacher assigned to this course.');
    }
    
    public function unassign_primary(string $id)
    {
        $course = Course::findOrFail($id);
        // Unassign the primary teacher from this course
        $course->teachers()->detach($course->teachers()->wherePivot('role', 'primary')->first()->id);
    
        return redirect()->route('assign_courses.index')->with('success', 'Primary teacher unassigned successfully.');
    }
    
    public function edit_secondary(string $course_id, string $teacher_id)
    {
        $course = Course::findOrFail($course_id);
        $current_teacher = User::findOrFail($teacher_id);
        $teachers = User::where('role', 'teacher')->get(); // Fetch teachers
        $isPrimary = false;

        return view('assign_courses.edit', [
            'course' => $course,
            'current_teacher' => $current_teacher,
            'teachers' => $teachers,
            'isPrimary' => $isPrimary,
        ]);
    }

    public function update_secondary(Request $request, string $course_id)
    {
        $request->validate([
            'new_teacher_id' => 'required|exists:users,id',
        ]);

        $course = Course::findOrFail($course_id);
        $teacher_id=$request->current_teacher_id;
        // Check if the selected teacher is already assigned as a primary teacher
        $isPrimaryTeacher = $course->teachers()
            ->wherePivot('teacher_id', $request->input('new_teacher_id'))
            ->wherePivot('role', 'primary')
            ->exists();

        if ($isPrimaryTeacher) {
            return redirect()->route('assign_courses.edit_secondary', [$course_id,$teacher_id])->withErrors(['new_teacher_id' => 'Selected teacher is already assigned as Primary to this course.']);
        }

        // Check if the selected teacher is already assigned as a secondary teacher
        $isSecondaryTeacher = $course->teachers()
            ->wherePivot('teacher_id', $request->input('new_teacher_id'))
            ->wherePivot('role', 'secondary')
            ->exists();

        if ($isSecondaryTeacher) {
            return redirect()->route('assign_courses.edit_secondary', [$course_id,$teacher_id])->withErrors(['new_teacher_id' => 'Selected teacher is already assigned as Secondary to this course.']);
        }

        // Find the secondary teachers for this course
        $secondaryTeachersCount = $course->teachers()->wherePivot('role', 'secondary')->count();

        if ($secondaryTeachersCount >= 5) { // Assuming maximum 5 secondary teachers allowed
            return redirect()->route('assign_courses.edit_secondary', [$course_id,$teacher_id])->withErrors(['new_teacher_id' => 'Maximum limit for Secondary teachers reached.']);
        }

        // Add the new teacher as a secondary teacher
        $course->teachers()->attach($request->input('new_teacher_id'), ['role' => 'secondary']);

        return redirect()->route('assign_courses.index')->with('success', 'Secondary teacher updated successfully.');
    }

    public function unassign_secondary(string $id)
    {
        $course = Course::findOrFail($id);
        // Unassign the secondary teacher from this course
        $course->teachers()->detach($course->teachers()->wherePivot('role', 'secondary')->first()->id);
    
        return redirect()->route('assign_courses.index')->with('success', 'Secondary teacher unassigned successfully.');
    }
    
    
}
