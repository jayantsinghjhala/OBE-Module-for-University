<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseOutcome;
use App\Models\Program;
use App\Models\Assessment;
use App\Models\School;
use App\Models\Student;
use App\Models\CourseOutcomeProgramOutcome;
use App\Models\AssessmentDetail;
use App\Models\StudentMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class CourseAttainmentController extends Controller
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

            // Filter courses that have data in the student_marks table
            $coursesWithMarks = $courses->filter(function ($course) {
                // Check if the course has at least one student mark
                return $course->course_assessments->isNotEmpty() && $course->course_assessments->flatMap->assessment_details->flatMap->student_marks->isNotEmpty();
            });

            // Load course outcomes, assessments, and assessment details for filtered courses
            foreach ($coursesWithMarks as $course) {
                $course->load(['course_outcomes', 'course_assessments.assessment_details.student_marks']);
            }

            return view('course_attainments.index', [
                'courses' => $coursesWithMarks,
                'schools' => $schools
            ]);
        } else {
            // Handle if the authenticated user is not a teacher
            // You may redirect or show an appropriate message
            return redirect()->route('home')->with('error', 'You do not have access to teacher courses.');
        }
    }

    public function attainment_index(Course $course)
    {
        // Calculate course attainment with a threshold of 60 (you can set your desired threshold)
        $courseAttainment = $this->calculateOverallAttainment($course->students,$course);

        return view('course_attainments.attainment_index', [
            'course' => $course,
            'courseAttainment' => $courseAttainment,
        ]);
    }

    // Function to calculate attainment level
    // Function to calculate attainment level
    private function calculateAttainmentLevel($percentage)
    {
        if ($percentage >= 80) {
            return 3;
        } elseif ($percentage >= 70) {
            return 2;
        } elseif ($percentage >= 60) {
            return 1;
        } else {
            return 0;
        }
    }

    public function calculatePOAttainment($overallAttainment, $course)
    {
        $courseOutcomes = $course->course_outcomes; // Assuming this fetches course outcomes for the given course
        $programOutcomes = $course->program->program_outcomes; // Assuming this fetches program outcomes for the course's program

        // Array to hold the mappings of COs with POs
        $avg_mapping = [];

        foreach ($programOutcomes as $programOutcome) {
            $coStrengths = $programOutcome->courseOutcomes->pluck('pivot.strength', 'id')->toArray();
            $totalStrength = array_sum($coStrengths);
            $avg_mapping[$programOutcome->id] = $totalStrength > 0 ? array_sum($coStrengths) / count($coStrengths) : 0;
        }

        // Calculate PO attainment levels based on the overall attainment and mappings
        $poAttainment = [];

        foreach ($avg_mapping as $poId => $m) {
            $poAttainment[$poId] = ($overallAttainment * $m) / 3;
        }
        $table_data = [
            'poAttainment' => $poAttainment,
            'avg_mapping' => $avg_mapping,
            'courseOutcomes' => $courseOutcomes,
            'programOutcomes' => $programOutcomes,
        ];
        // dd ($table_data);
        return $table_data;
    }

    // Function to calculate overall attainment
    public function calculateOverallAttainment($students,$course)
    {
        $threshold = 60; // Threshold percentage
        $totalStudents = count($students);
        $ciaAttainment = [];
        $etaAttainment = [];
        $coStudentsAboveThresholdCIA = [];
        $coStudentsAboveThresholdETA = [];

        foreach ($students as $student) {
            $ciaMarks = [];
            $etaMarks = [];
            $studentsAboveThresholdCIA = [];
            $studentsAboveThresholdETA = [];

            foreach ($student->student_marks as $mark) {
                $assessmentType = $mark->assessment_detail->course_assessment->assessment->assessment_type;

                $coId = $mark->assessment_detail->course_outcome_id;
                $maxMarks_CIA = $mark->assessment_detail->course_outcome->max_marks_CIA;
                $maxMarks_ETA = $mark->assessment_detail->course_outcome->max_marks_ETA;
                $obtainedMarks = (float) $mark->obtained_marks;

                if ($assessmentType === 'CIA') {
                    // dd($obtainedMarks / $maxMarks_CIA * 100);
                    $ciaMarks[$coId][] = $obtainedMarks / $maxMarks_CIA * 100;

                   
                } elseif ($assessmentType === 'ETA') {
                    $etaMarks[$coId][] = $obtainedMarks / $maxMarks_ETA * 100;

                    
                }
            }
            foreach ($ciaMarks as $coId => $percentages) {
                $sum = array_sum($percentages);
                $ciaMarks[$coId] = [$sum]; // Replace array of percentages with the sum
                if ($sum >= $threshold) {
                    $studentsAboveThresholdCIA[$coId] = isset($studentsAboveThresholdCIA[$coId]) ? $studentsAboveThresholdCIA[$coId] + 1 : 1;
                }
            }
            foreach ($etaMarks as $coId => $percentages) {
                $sum = array_sum($percentages);
                $etaMarks[$coId] = [$sum]; // Replace array of percentages with the sum
                if ($sum >= $threshold) {
                    $studentsAboveThresholdETA[$coId] = isset($studentsAboveThresholdETA[$coId]) ? $studentsAboveThresholdETA[$coId] + 1 : 1;
                }
            }
// dd($ciaMarks);
            foreach ($studentsAboveThresholdCIA as $coId => $aboveThreshold) {
                $coStudentsAboveThresholdCIA[$coId] = isset($coStudentsAboveThresholdCIA[$coId]) ? $coStudentsAboveThresholdCIA[$coId] + 1 : 1;
            }
// dd($studentsAboveThresholdCIA);
            foreach ($studentsAboveThresholdETA as $coId => $aboveThreshold) {
                $coStudentsAboveThresholdETA[$coId] = isset($coStudentsAboveThresholdETA[$coId]) ? $coStudentsAboveThresholdETA[$coId] + 1 : 1;
            }

            foreach ($ciaMarks as $coId => $marks) {
                $ciaPercentage = array_sum($marks) / count($marks);
                // dd($ciaPercentage);
                $ciaAttainment[$coId][] = $this->calculateAttainmentLevel($ciaPercentage);
            }

            foreach ($etaMarks as $coId => $marks) {
                $etaPercentage = array_sum($marks) / count($marks);
                $etaAttainment[$coId][] = $this->calculateAttainmentLevel($etaPercentage);
            }
        }
        // dd($ciaMarks, $ciaAttainment, $etaMarks, $etaAttainment);

        // Calculate CO-wise percentage of students above threshold for CIA
        $coThresholdPercentagesCIA = [];
        foreach ($coStudentsAboveThresholdCIA as $coId => $count) {
            $coThresholdPercentagesCIA[$coId] = ($count / $totalStudents) * 100;
        }

        // Calculate CO-wise percentage of students above threshold for ETA
        $coThresholdPercentagesETA = [];
        foreach ($coStudentsAboveThresholdETA as $coId => $count) {
            $coThresholdPercentagesETA[$coId] = ($count / $totalStudents) * 100;
        }
        // Calculate average CIA and ETA attainment for each CO
        $overallCIAAttainment = [];
        $overallETAAttainment = [];

        foreach ($ciaAttainment as $coId => $attainment) {
            $overallCIAAttainment[$coId] = count($attainment) > 0 ? array_sum($attainment) / count($attainment) : 0;
        }

        foreach ($etaAttainment as $coId => $attainment) {
            $overallETAAttainment[$coId] = count($attainment) > 0 ? array_sum($attainment) / count($attainment) : 0;
        }

        // Calculate weighted overall attainment
        $overallCIA = count($overallCIAAttainment) > 0 ? array_sum($overallCIAAttainment) / count($overallCIAAttainment) : 0;
        $overallETA = count($overallETAAttainment) > 0 ? array_sum($overallETAAttainment) / count($overallETAAttainment) : 0;
        $overallAttainment = (0.4 * $overallCIA) + (0.6 * $overallETA);
        $table_data = $this->calculatePOAttainment($overallAttainment, $course);

        $weightageCIAAttainment = [];
        $weightageETAAttainment = [];

        foreach ($overallCIAAttainment as $coId => $attainment) {
            $weightageCIAAttainment[$coId] = $attainment * 0.4;
        }

        foreach ($overallETAAttainment as $coId => $attainment) {
            $weightageETAAttainment[$coId] = $attainment * 0.6;
        }

        // dd($coThresholdPercentagesCIA,$coStudentsAboveThresholdCIA);
        return [
            'ciaAttainment' => $ciaAttainment,
            'etaAttainment' => $etaAttainment,
            'overallCIAAttainment' => $overallCIAAttainment,
            'overallETAAttainment' => $overallETAAttainment,
            'weightageCIAAttainment' => $overallCIA * 0.4,
            'weightageETAAttainment' => $overallETA * 0.6,
            'overallAttainment' => $overallAttainment,
            'coThresholdPercentagesCIA' => $coThresholdPercentagesCIA,
            'coThresholdPercentagesETA' => $coThresholdPercentagesETA,
            'coStudentsAboveThresholdCIA' => $coStudentsAboveThresholdCIA,
            'coStudentsAboveThresholdETA' => $coStudentsAboveThresholdETA,
            'overallCIA'=>$overallCIA,
            'overallETA'=>$overallETA,
            'table_data' => $table_data,

        ];
    }

    
 

    // public function calculateOverallAttainment($students)
    // {
    //     $threshold = 90; // Threshold percentage
    //     $totalStudents = count($students);
    //     $ciaAttainment = [];
    //     $etaAttainment = [];
    //     $coStudentsAboveThreshold = []; // Store the count of students above the threshold for each CO

    //     foreach ($students as $student) {
    //         $ciaMarks = []; // Store CIA marks for each CO
    //         $etaMarks = []; // Store ETA marks for each CO

    //         // Fetch CIA and ETA marks for the student
    //         foreach ($student->student_marks as $mark) {
    //             $assessmentType = $mark->assessment_detail->course_assessment->assessment->assessment_type;

    //             $coId = $mark->assessment_detail->course_outcome_id;
    //             $maxMarks_CIA = $mark->assessment_detail->course_outcome->max_marks_CIA;
    //             $maxMarks_ETA = $mark->assessment_detail->course_outcome->max_marks_ETA;
    //             $obtainedMarks = (float) $mark->obtained_marks;

    //             if ($assessmentType === 'CIA') {
    //                 $ciaMarks[$coId][] = $obtainedMarks / $maxMarks_CIA * 100;
    //             } elseif ($assessmentType === 'ETA') {
    //                 $etaMarks[$coId][] = $obtainedMarks / $maxMarks_ETA * 100;
    //             }
    //         }


    //         // Calculate CIA and ETA attainment for each CO for the student
    //         foreach ($ciaMarks as $coId => $marks) {
    //             $ciaPercentage = array_sum($marks) / count($marks);
    //             $attainment = $this->calculateAttainmentLevel($ciaPercentage);
    //             $ciaAttainment[$coId][] = $attainment; // Ensure attainment values are added to the array

    //             // Count students above threshold for this CO
    //             if ($attainment >= $threshold / 100 * 3) {
    //                 $coStudentsAboveThreshold[$coId] = isset($coStudentsAboveThreshold[$coId]) ?
    //                     $coStudentsAboveThreshold[$coId] + 1 : 1;
    //             }
    //         }

    //         foreach ($etaMarks as $coId => $marks) {
    //             $etaPercentage = array_sum($marks) / count($marks);
    //             $attainment = $this->calculateAttainmentLevel($etaPercentage);
    //             $etaAttainment[$coId][] = $attainment; // Ensure attainment values are added to the array

    //             // Count students above threshold for this CO
    //             if ($attainment >= $threshold / 100 * 3) {
    //                 $coStudentsAboveThreshold[$coId] = isset($coStudentsAboveThreshold[$coId]) ?
    //                     $coStudentsAboveThreshold[$coId] + 1 : 1;
    //             }
    //         }

    //         // Debug points to inspect intermediate values
    //         dd($ciaMarks, $etaMarks, $ciaAttainment, $etaAttainment);
    //     }

    //     // Calculate CO-wise percentage of students above threshold
    //     $coThresholdPercentages = [];
    //     foreach ($coStudentsAboveThreshold as $coId => $count) {
    //         $coThresholdPercentages[$coId] = ($count / $totalStudents) * 100;
    //     }

    //     // Calculate average CIA and ETA attainment for each CO
    //     $overallCIAAttainment = [];
    //     $overallETAAttainment = [];

    //     foreach ($ciaAttainment as $coId => $attainment) {
    //         $overallCIAAttainment[$coId] = count($attainment) > 0 ? array_sum($attainment) / count($attainment) : 0;
    //     }

    //     foreach ($etaAttainment as $coId => $attainment) {
    //         $overallETAAttainment[$coId] = count($attainment) > 0 ? array_sum($attainment) / count($attainment) : 0;
    //     }

    //     // dd($ciaAttainment, $etaAttainment, $overallCIAAttainment, $overallETAAttainment);

    //     $overallCIA = count($overallCIAAttainment) > 0 ? array_sum($overallCIAAttainment) / count($overallCIAAttainment) : 0;
    //     $overallETA = count($overallETAAttainment) > 0 ? array_sum($overallETAAttainment) / count($overallETAAttainment) : 0;

    //     // dd($overallCIA, $overallETA);

    //     // Calculate weighted overall attainment
    //     $overallAttainment = (0.4 * $overallCIA) + (0.6 * $overallETA);

    //     dd($overallAttainment);
    //     return $overallAttainment;
    // }
    // public function calculateCourseAttainment(Course $course)
    // {
    //     $course->load(['course_outcomes', 'course_assessments.assessment_details.student_marks']);

    //     $totalCOs = count($course->course_outcomes);
    //     $totalStudents = count($course->students);

    //     $threshold = 60; // Target attainment threshold percentage

    //     $ciaAttainments = [];
    //     $etaAttainments = [];
    //     $coWiseAttainment = [];
    //     $totalAssessments = 0;

    //     foreach ($course->course_outcomes as $key => $co) {
    //         $assessmentDetails = AssessmentDetail::where('course_outcome_id', $co->id)
    //             ->with('student_marks')
    //             ->get();

    //         $totalCOStudents = count($course->students);

    //         $studentsAboveThreshold = 0;

    //         foreach ($assessmentDetails as $detail) {
    //             $assessmentType = $detail->course_assessment->assessment->assessment_type;

    //             // Check if student_marks exist
    //             if ($detail->student_marks->isNotEmpty()) {
    //                 foreach ($detail->student_marks as $studentMark) {
    //                     $totalAssessments++; // Increment total assessments
    //                     $attainmentLevel = ($studentMark->obtained_marks / $detail->course_assessment->maximum_marks) * 100;

    //                     // Store attainment level student-wise for each assessment type
    //                     if ($assessmentType === 'CIA') {
    //                         $ciaAttainments[$studentMark->student_id][] = $attainmentLevel;
    //                     } elseif ($assessmentType === 'ETA') {
    //                         $etaAttainments[$studentMark->student_id][] = $attainmentLevel;
    //                     }
    //                 }
    //             }
    //         }

    //         // Calculate CO Attainment
    //         $coAttainment = ($studentsAboveThreshold / $totalCOStudents) * 100;

    //         // Fill in CO Attainment details for each CO (if needed)
    //         $coWiseAttainment[$key + 1]['Direct CO Attainment'] = $coAttainment;
    //         $coWiseAttainment[$key + 1]['Attainment Level'] = $this->calculateAttainmentLevel($coAttainment);
    //     }

    //     // Calculate average CIA and ETA attainments for each CO
    //     $averageCIAAttainments = [];
    //     $averageETAAttainments = [];

    //     foreach ($ciaAttainments as $coIndex => $attainmentValues) {
    //         $averageCIAAttainments[] = count($attainmentValues) > 0 ? (array_sum($attainmentValues) / count($attainmentValues)) : 0;
    //     }

    //     foreach ($etaAttainments as $coIndex => $attainmentValues) {
    //         $averageETAAttainments[] = count($attainmentValues) > 0 ? (array_sum($attainmentValues) / count($attainmentValues)) : 0;
    //     }

    //     // Calculate overall CIA and ETA attainments
    //     $overallCIAAttainment = count($averageCIAAttainments) > 0 ? (array_sum($averageCIAAttainments) / count($averageCIAAttainments)) : 0;
    //     $overallETAAttainment = count($averageETAAttainments) > 0 ? (array_sum($averageETAAttainments) / count($averageETAAttainments)) : 0;

    //     // Calculate the final overall course attainment based on weights
    //     $overallCourseAttainment = (0.4 * $overallCIAAttainment) + (0.6 * $overallETAAttainment);
    //     $weightageCIAAttainment = 0.4 * $overallCIAAttainment;
    //     $weightageETAAttainment = 0.6 * $overallETAAttainment;

    //     return [
    //         'overallCIAAttainment' => $overallCIAAttainment,
    //         'overallETAAttainment' => $overallETAAttainment,
    //         'overallCourseAttainment' => $overallCourseAttainment,
    //         'totalCOs' => $totalCOs,
    //         'totalAssessments' => $totalAssessments,
    //         'totalStudents' => $totalStudents,
    //         'coWiseAttainment' => $coWiseAttainment,
    //         'weightageCIAAttainment' => $weightageCIAAttainment,
    //         'weightageETAAttainment' => $weightageETAAttainment
    //     ];
    // }


    // // Function to fetch maximum marks for a particular CO
    // private function fetchMaxMarksForCO($coId)
    // {
    //     $assessmentDetails = AssessmentDetail::where('course_outcome_id', $coId)
    //         ->get();

    //     // Calculate maximum marks for this CO
    //     $maxMarks = 0;
    //     foreach ($assessmentDetails as $detail) {
    //         // Sum all the marks from assessment details associated with this CO
    //         $maxMarks += $detail->marks;
    //     } // Assuming $coMaximumMarks contains CO maximum marks
    //     return $maxMarks;
    // }


    // // Function to calculate Attainment Level based on percentage
    // private function calculateAttainmentLevel($percentage)
    // {
    //     if ($percentage >= 80) {
    //         return 3; // Attainment level 3 if more than 80% achieved
    //     } elseif ($percentage >= 70) {
    //         return 2; // Attainment level 2 if >70% achieved
    //     } elseif ($percentage >= 60) {
    //         return 1; // Attainment level 1 if >60% achieved
    //     } else {
    //         return 0; // Attainment level 0 if less than 60%
    //     }
    // }
//     public function calculateOverallAttainment($students)
//     {
//         $threshold = 60; // Threshold percentage
//         $totalStudents = count($students);
//         $ciaAttainment = [];
//         $etaAttainment = [];
//         $coStudentsAboveThresholdCIA = [];
//         $coStudentsAboveThresholdETA = [];

//         foreach ($students as $student) {
//             $ciaMarks = [];
//             $etaMarks = [];
//             $studentsAboveThresholdCIA = [];
//             $studentsAboveThresholdETA = [];

//             foreach ($student->student_marks as $mark) {
//                 $assessmentType = $mark->assessment_detail->course_assessment->assessment->assessment_type;

//                 $coId = $mark->assessment_detail->course_outcome_id;
//                 $maxMarks_CIA = $mark->assessment_detail->course_outcome->max_marks_CIA;
//                 $maxMarks_ETA = $mark->assessment_detail->course_outcome->max_marks_ETA;
//                 $obtainedMarks = (float) $mark->obtained_marks;

//                 if ($assessmentType === 'CIA') {
//                     // dd($obtainedMarks / $maxMarks_CIA * 100);
//                     $ciaMarks[$coId][] = $obtainedMarks / $maxMarks_CIA * 100;

//                     if (($obtainedMarks / $maxMarks_CIA * 100) >= $threshold) {
//                         $studentsAboveThresholdCIA[$coId] = isset($studentsAboveThresholdCIA[$coId]) ? $studentsAboveThresholdCIA[$coId] + 1 : 1;
//                     }
//                 } elseif ($assessmentType === 'ETA') {
//                     $etaMarks[$coId][] = $obtainedMarks / $maxMarks_ETA * 100;

//                     if (($obtainedMarks / $maxMarks_ETA * 100) >= $threshold) {
//                         $studentsAboveThresholdETA[$coId] = isset($studentsAboveThresholdETA[$coId]) ? $studentsAboveThresholdETA[$coId] + 1 : 1;
//                     }
//                 }
//             }
// // dd($ciaMarks);
//             foreach ($studentsAboveThresholdCIA as $coId => $aboveThreshold) {
//                 $coStudentsAboveThresholdCIA[$coId] = isset($coStudentsAboveThresholdCIA[$coId]) ? $coStudentsAboveThresholdCIA[$coId] + 1 : 1;
//             }

//             foreach ($studentsAboveThresholdETA as $coId => $aboveThreshold) {
//                 $coStudentsAboveThresholdETA[$coId] = isset($coStudentsAboveThresholdETA[$coId]) ? $coStudentsAboveThresholdETA[$coId] + 1 : 1;
//             }

//             foreach ($ciaMarks as $coId => $marks) {
//                 $ciaPercentage = array_sum($marks) / count($marks);
//                 $ciaAttainment[$coId][] = $this->calculateAttainmentLevel($ciaPercentage);
//             }

//             foreach ($etaMarks as $coId => $marks) {
//                 $etaPercentage = array_sum($marks) / count($marks);
//                 $etaAttainment[$coId][] = $this->calculateAttainmentLevel($etaPercentage);
//             }
//         }
//         dd($ciaMarks, $ciaAttainment, $etaMarks, $etaAttainment);

//         // Calculate CO-wise percentage of students above threshold for CIA
//         $coThresholdPercentagesCIA = [];
//         foreach ($coStudentsAboveThresholdCIA as $coId => $count) {
//             $coThresholdPercentagesCIA[$coId] = ($count / $totalStudents) * 100;
//         }

//         // Calculate CO-wise percentage of students above threshold for ETA
//         $coThresholdPercentagesETA = [];
//         foreach ($coStudentsAboveThresholdETA as $coId => $count) {
//             $coThresholdPercentagesETA[$coId] = ($count / $totalStudents) * 100;
//         }
//         // Calculate average CIA and ETA attainment for each CO
//         $overallCIAAttainment = [];
//         $overallETAAttainment = [];

//         foreach ($ciaAttainment as $coId => $attainment) {
//             $overallCIAAttainment[$coId] = count($attainment) > 0 ? array_sum($attainment) / count($attainment) : 0;
//         }

//         foreach ($etaAttainment as $coId => $attainment) {
//             $overallETAAttainment[$coId] = count($attainment) > 0 ? array_sum($attainment) / count($attainment) : 0;
//         }

//         // Calculate weighted overall attainment
//         $overallCIA = count($overallCIAAttainment) > 0 ? array_sum($overallCIAAttainment) / count($overallCIAAttainment) : 0;
//         $overallETA = count($overallETAAttainment) > 0 ? array_sum($overallETAAttainment) / count($overallETAAttainment) : 0;
//         $overallAttainment = (0.4 * $overallCIA) + (0.6 * $overallETA);

//         $weightageCIAAttainment = [];
//         $weightageETAAttainment = [];

//         foreach ($overallCIAAttainment as $coId => $attainment) {
//             $weightageCIAAttainment[$coId] = $attainment * 0.4;
//         }

//         foreach ($overallETAAttainment as $coId => $attainment) {
//             $weightageETAAttainment[$coId] = $attainment * 0.6;
//         }

//         return [
//             'ciaAttainment' => $ciaAttainment,
//             'etaAttainment' => $etaAttainment,
//             'overallCIAAttainment' => $overallCIAAttainment,
//             'overallETAAttainment' => $overallETAAttainment,
//             'weightageCIAAttainment' => array_sum($overallCIAAttainment) * 0.4,
//             'weightageETAAttainment' => array_sum($overallETAAttainment) * 0.6,
//             'overallAttainment' => $overallAttainment,
//             'coThresholdPercentagesCIA' => $coThresholdPercentagesCIA,
//             'coThresholdPercentagesETA' => $coThresholdPercentagesETA,
//             'coStudentsAboveThresholdCIA' => $coStudentsAboveThresholdCIA,
//             'coStudentsAboveThresholdETA' => $coStudentsAboveThresholdETA,
//         ];
//     }




}
