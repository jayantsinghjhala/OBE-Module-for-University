<?php

namespace App\Http\Controllers;

use App\Models\CourseAssessment;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseOutcome;
use App\Models\Program;
use App\Models\Assessment;
use App\Models\School;
use App\Models\CourseOutcomeProgramOutcome;
use App\Models\AssessmentDetail;
use App\Models\StudentMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CourseOutcomeController extends Controller
{

    public function index()
    {
        // Get the currently authenticated user
        $currentUser = Auth::user();

        // Check if the user is authenticated and has the role of 'teacher'
        if ($currentUser && $currentUser->role === 'teacher') {
            // Retrieve the courses being taught by the current teacher
            $courses = $currentUser->courses()->orderBy('created_at', 'desc')->get();
            $schools = School::all();

            return view('course_outcomes.index', [
                'courses' => $courses,
                'schools' => $schools
            ]);
        } else {
            // Handle if the authenticated user is not a teacher
            // You may redirect or show an appropriate message
            return redirect()->route('home')->with('error', 'You do not have access to teacher courses.');
        }
    }

    public function create(Course $course)
    {
        return view('course_outcomes.create', [
            'course' => $course
        ]);
    }


    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_id' => 'required|integer',
            'name' => 'required|string',
            'outcome_description' => 'required|string',
        ]);

        //Check if the combination of course_id and name already exists
        $existingOutcome = CourseOutcome::where('course_id', $validated['course_id'])
            ->where('name', $validated['name'])
            ->first();

        if ($existingOutcome) {
            return redirect()->back()->withInput()->withErrors([
                'name' => 'This outcome name already exists for the selected course.'
            ]);
        }

        $course_outcomes = new CourseOutcome();
        $course_outcomes->course_id = $validated['course_id'];
        $course_outcomes->name = $validated['name'];
        $course_outcomes->outcome_description = $validated['outcome_description'];
        $course_outcomes->save();

        // Redirect to the index view with a success message
        return redirect()->route('course_outcomes.add_outcomes', $course)->with('success', 'Course Outcome created successfully');
    }

    public function show($id)
    {
        $course = Course::findOrFail($id);
        return view('course_outcomes.show', [
            'course' => $course
        ]);
    }

    public function edit(Course $course, CourseOutcome $course_outcome)
    {
        return view('course_outcomes.edit', [
            'course' => $course,
            'course_outcome' => $course_outcome
        ]);
    }

    public function update(Request $request, Course $course, CourseOutcome $course_outcome)
    {
        $validated = $request->validate([
            'course_id' => 'required|integer',
            'name' => 'required|string',
            'outcome_description' => 'required|string',
        ]);

        // Check if the name has changed
        if ($course_outcome->name !== $validated['name']) {
            // If the name has changed, check if the combination of course_id and name already exists
            $existingOutcome = CourseOutcome::where('course_id', $validated['course_id'])
                ->where('name', $validated['name'])
                ->first();

            if ($existingOutcome) {
                return redirect()->back()->withInput()->withErrors([
                    'name' => 'This outcome name already exists for the selected course.'
                ]);
            }
        }

        // Update the course outcome
        $course_outcome->update([
            'course_id' => $validated['course_id'],
            'name' => $validated['name'],
            'outcome_description' => $validated['outcome_description'],
        ]);

        return redirect()->route('course_outcomes.add_outcomes', $course)->with('success', 'Course Outcome updated successfully');
    }

    public function destroy(Course $course, CourseOutcome $course_outcome)
    {
        $course_outcome->delete();
        return redirect()->route('course_outcomes.add_outcomes', $course);
    }

    public function add_outcomes(Course $course)
    {
        // Get all related course outcomes for this course
        $course_outcomes = $course->course_outcomes;
        $schools = School::all();
        return view('course_outcomes.co_index', [
            'course' => $course,
            'course_outcomes' => $course_outcomes,
            'schools' => $schools
        ]);
    }

    public function map_outcomes(Course $course)
    {
        $course_outcomes = $course->course_outcomes;
        $program = $course->program;
        $program_outcomes = $program->program_outcomes;


        return view('course_outcomes.map_co_index', [
            'course' => $course,
            'course_outcomes' => $course_outcomes,
            'program_outcomes' => $program_outcomes
        ]);
    }

    public function save_mapping(Request $request, Course $course)
    {
        // Retrieve the CO-PO pairs from the form submission
        $co_po_pairs = $request->input('co_po_pairs');

        foreach ($co_po_pairs as $pair) {
            // Split the pair into CO ID, PO ID, and strength
            list($co_id, $po_id, $strength) = explode('_', $pair);

            // Find existing record for the combination
            $existingRecord = CourseOutcomeProgramOutcome::where('course_outcome_id', $co_id)
                ->where('program_outcome_id', $po_id)
                ->first();

            if ($strength == 0) {
                // Delete record if the new strength is zero
                if ($existingRecord) {
                    $existingRecord->delete();
                }
            } else {
                // Update strength if different from the previous one, or create new record if not existing
                if ($existingRecord) {
                    if ($existingRecord->strength != $strength) {
                        $existingRecord->update(['strength' => $strength]);
                    }
                } else {
                    CourseOutcomeProgramOutcome::create([
                        'course_outcome_id' => $co_id,
                        'program_outcome_id' => $po_id,
                        'strength' => $strength,
                        // Other fields if needed
                    ]);
                }
            }
        }

        $course_outcomes = $course->course_outcomes;
        $program = $course->program;
        $program_outcomes = $program->program_outcomes;

        return view('course_outcomes.map_co_index', [
            'course' => $course,
            'course_outcomes' => $course_outcomes,
            'program_outcomes' => $program_outcomes
        ]);
    }

    public function update_status(Request $request, CourseOutcome $course_outcome)
    {
        $course_outcome->status = $request->status;
        $course_outcome->save();

        return response()->json(['success' => true]);
    }

    public function course_assessments(Course $course)
    {
        $assessments = Assessment::orderBy('created_at', 'desc')->get();

        return view('course_outcomes.course_assessments', [
            'course' => $course,
            'assessments' => $assessments,
        ]);

    }

    public function question_outcome(Course $course)
    {
        $course_outcomes = $course->course_outcomes;
        $course_assessments = $course->course_assessments;

        foreach ($course_assessments as $courseAssessment) {
            $assessmentDetails = $courseAssessment->assessment()->select('assessment_name', 'assessment_type')->first();

            // Attach assessment details to each course assessment object
            $courseAssessment->assessment_name = $assessmentDetails->assessment_name ?? null;
            $courseAssessment->assessment_type = $assessmentDetails->assessment_type ?? null;
        }
        return view('course_outcomes.question_outcome', [
            'course' => $course,
            'course_outcomes' => $course_outcomes,
            'course_assessments' => $course_assessments // Pass assessments data to the view
        ]);
    }

    public function question_outcome_store(Request $request, $course_assessment_id)
    {
        // Get the assessment details array from the request
        $assessmentDetails = $request->input('assessment_details');
        $course_assessment=CourseAssessment::findOrFail($course_assessment_id);
        $assessment_type=$course_assessment->assessment->assessment_type;
        
        // Array to store created or updated assessment details
        $createdUpdatedAssessmentDetails = [];

        // Loop through the received assessment details
        foreach ($assessmentDetails as $detail) {
            $new_course_outcome=CourseOutcome::findOrFail($detail['course_outcome_id']);
            if ($detail['id'] != -1) {
                // Find the existing assessment detail based on the ID
                try {
                    $existingDetail = AssessmentDetail::findOrFail($detail['id']);
                    
                    if ($assessment_type == 'CIA') {
                        if($detail['course_outcome_id']!=$existingDetail->course_outcome_id){
                            $old_course_outcome=CourseOutcome::findOrFail($existingDetail->course_outcome_id);
                            $new_course_outcome->max_marks_CIA+=$detail['marks'];
                            $old_course_outcome->max_marks_CIA-=$existingDetail->marks;
                            $old_course_outcome->save();
                            $new_course_outcome->save();
                        }else{
                            $new_course_outcome->max_marks_CIA-=$existingDetail->marks;
                            $new_course_outcome->max_marks_CIA+=$detail['marks'];
                            $new_course_outcome->save();
                        }                        
                    }
                    else if ($assessment_type == 'ETA') {
                        if($detail['course_outcome_id']!=$existingDetail->course_outcome_id){
                            $old_course_outcome=CourseOutcome::findOrFail($existingDetail->course_outcome_id);
                            $new_course_outcome->max_marks_ETA+=$detail['marks'];
                            $old_course_outcome->max_marks_ETA-=$existingDetail->marks;
                            $old_course_outcome->save();
                            $new_course_outcome->save();
                        }else{
                            $new_course_outcome->max_marks_ETA-=$existingDetail->marks;
                            $new_course_outcome->max_marks_ETA+=$detail['marks'];
                            $new_course_outcome->save();
                        }
                    }
                    // Update the existing detail
                    $existingDetail->update([
                        'marks' => $detail['marks'],
                        'course_outcome_id' => $detail['course_outcome_id']
                    ]);

                    // Add the updated detail to the array
                    $createdUpdatedAssessmentDetails[] = $existingDetail;
                } catch (ModelNotFoundException $exception) {
                    // Handle the case where the record is not found
                    // This catch block will execute if the record with the given ID does not exist
                    return response()->json(['error' => 'Record not found'], 404);
                }
            } else {
                if ($assessment_type == 'CIA') {
                    $new_course_outcome->max_marks_CIA+=$detail['marks'];
                    $new_course_outcome->save();
                }
                else if ($assessment_type == 'ETA') {
                    $new_course_outcome->max_marks_ETA+=$detail['marks'];
                    $new_course_outcome->save();
                }
                // Create a new AssessmentDetail instance and store the details
                $assessmentDetail = AssessmentDetail::create([
                    'course_assessment_id' => $course_assessment_id,
                    'course_outcome_id' => $detail['course_outcome_id'],
                    'question_number' => $detail['question_number'],
                    'marks' => $detail['marks'],
                ]);

                // Add the created detail to the array
                $createdUpdatedAssessmentDetails[] = $assessmentDetail;
            }
        }

        // Return a response indicating successful storage or updates
        return response()->json([
            'message' => 'Assessment details stored or updated successfully',
            'assessment_details' => $createdUpdatedAssessmentDetails
        ], 200);
    }



    public function get_assessment_details(Request $request)
    {
        $courseAssessmentId = $request->input('course_assessment_id');

        // Fetch assessment details based on the ID
        $assessmentDetails = AssessmentDetail::where('course_assessment_id', $courseAssessmentId)->get();

        // Fetch course outcome names for each assessment detail
        foreach ($assessmentDetails as $detail) {
            $courseOutcome = CourseOutcome::find($detail->course_outcome_id);
            if ($courseOutcome) {
                $detail->course_outcome_name = $courseOutcome->name;
            }
        }

        return response()->json(['assessment_details' => $assessmentDetails]);
    }

    


    public function student_marks(Course $course)
    {
        $course_assessments = $course->course_assessments->filter(function ($courseAssessment) {
            // Count the number of assessment details related to this course assessment
            $numAssessmentDetails = $courseAssessment->assessment_details->count();

            // Compare the number of assessment details with the num_questions attribute
            return $numAssessmentDetails === $courseAssessment->num_questions;
        });

        $students = $course->students;

        foreach ($course_assessments as $courseAssessment) {
            $assessmentDetails = $courseAssessment->assessment()->select('assessment_name', 'assessment_type')->first();

            // Attach assessment details to each course assessment object
            $courseAssessment->assessment_name = $assessmentDetails->assessment_name ?? null;
            $courseAssessment->assessment_type = $assessmentDetails->assessment_type ?? null;
        }

        return view('course_outcomes.student_marks', [
            'course' => $course,
            'course_assessments' => $course_assessments, // Pass assessments data to the view
            'students' => $students
        ]);
    }


    public function student_marks_store(Request $request)
    {
        // Retrieve student marks array from the request
        $studentMarks = $request->input('student_marks');

        // Array to store created/updated student marks
        $createdUpdatedStudentMarks = [];

        // Loop through the received student marks
        foreach ($studentMarks as $mark) {
            if ($mark['id'] != -1) {
                try {
                    // Find the existing student mark based on the ID
                    $existingStudentMark = StudentMark::findOrFail($mark['id']);

                    // Update the existing student mark
                    $existingStudentMark->update([
                        'obtained_marks' => $mark['obtained_marks']
                        // Add other fields if needed
                    ]);

                    // Add the updated student mark to the array
                    $createdUpdatedStudentMarks[] = $existingStudentMark;
                } catch (ModelNotFoundException $exception) {
                    return response()->json(['error' => 'Record not found'], 404);
                }
            } else {
                // Create a new StudentMark instance and store the details
                $studentMark = StudentMark::create([
                    'assessment_detail_id' => $mark['assessment_detail_id'],
                    'student_id' => $mark['student_id'],
                    'obtained_marks' => $mark['obtained_marks'],
                    // Optionally, include 'created_at' and 'updated_at' timestamps here
                ]);

                // Add the created student mark to the array
                $createdUpdatedStudentMarks[] = $studentMark;
            }
        }

        // Return a JSON response with the created/updated student marks
        return response()->json(['marks_details' => $createdUpdatedStudentMarks], 201);
    }

    public function fetch_existing_marks(Request $request)
    {
        // Get assessment details and students from the request
        $assessmentDetails = $request->input('assessmentDetails');
        $students = $request->input('students');

        // Fetch existing marks from the database
        $existingMarks = [];

        foreach ($students as $student) {
            $existingMarks[$student['id']] = [];

            foreach ($assessmentDetails as $detail) {
                $existingMark = StudentMark::where('assessment_detail_id', $detail['id'])
                    ->where('student_id', $student['id'])
                    ->get();

                // Store existing marks in the response array
                $existingMarks[$student['id']][$detail['id']] = $existingMark;
            }
        }

        return response()->json($existingMarks);
    }


}
