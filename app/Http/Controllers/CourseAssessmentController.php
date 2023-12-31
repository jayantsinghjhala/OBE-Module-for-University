<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseAssessment;

class CourseAssessmentController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'assessment_id' => 'required|exists:assessments,id',
            'num_questions' => 'nullable|integer|min:1',
            'maximum_marks' => 'nullable|numeric|min:0',
            'assessment_date' => 'nullable|date',
        ]);

        // Create a new course assessment
        $course_assessment = CourseAssessment::create($validatedData);

        // Fetch associated assessment details
        $assessmentDetails = $course_assessment->assessment()->select('assessment_name', 'assessment_type')->first();

        // Merge assessment details into the response
        $response = [
            'message' => 'Course assessment created successfully',
            'course_assessment' => $course_assessment,
            'assessment_name' => $assessmentDetails->assessment_name,
            'assessment_type' => $assessmentDetails->assessment_type,
        ];

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'num_questions' => 'required|integer|min:1',
            'maximum_marks' => 'required|numeric|min:0',
            'assessment_date' => 'nullable|date',
        ]);

        // Find the course assessment
        $courseAssessment = CourseAssessment::findOrFail($id);
        // Update the course assessment
        $courseAssessment->update($validatedData);

        return response()->json(['message' => 'Course assessment updated successfully', 'data' => $courseAssessment]);
    }

    public function destroy($id)
    {
        // Find the course assessment
        $courseAssessment = CourseAssessment::findOrFail($id);

        // Delete the course assessment
        $courseAssessment->delete();

        return response()->json(['message' => 'Course assessment deleted successfully']);
    }
    public function getCourseAssessment($assessment_id, $course_id)
    {
        $courseAssessmentExists = CourseAssessment::where('assessment_id', $assessment_id)
            ->where('course_id', $course_id)
            ->exists();

        return response()->json(['exists' => $courseAssessmentExists]);
    }

    // Method to fetch all course assessments for a specific course
    public function getCourseAssessmentsForCourse($course_id)
    {
        // Fetch course assessments for the given course_id
        $courseAssessments = CourseAssessment::where('course_id', $course_id)->get();

        // Fetch associated assessment details for each course assessment
        foreach ($courseAssessments as $courseAssessment) {
            $assessmentDetails = $courseAssessment->assessment()->select('assessment_name', 'assessment_type')->first();

            // Attach assessment details to each course assessment object
            $courseAssessment->assessment_name = $assessmentDetails->assessment_name ?? null;
            $courseAssessment->assessment_type = $assessmentDetails->assessment_type ?? null;
        }

        // Return the updated course assessments with assessment details
        return response()->json($courseAssessments);
    }

    
}
