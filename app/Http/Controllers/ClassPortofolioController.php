<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\CourseClass;
use App\Models\LessonLearningOutcome;
use Illuminate\Support\Facades\DB;

class ClassPortofolioController extends Controller
{
    /**
     * @param CourseClass $courseClass
     * @return Application|Factory|View
     */
    public function index(CourseClass $courseClass)
    {
        $this->authorize('view', [CourseClass::class, $courseClass]);

        $courseClass->load('syllabus.lessonLearningOutcomes');

        $llo_threshold = $courseClass->settings->llo_threshold ?? null;
        if (empty($llo_threshold)) {
            return view('class-portofolio.index', [
                'courseClass' => $courseClass,
                'lloThreshold' => $llo_threshold
            ]);
        }

        $totalStudentsCount = $courseClass->students()->count();
        $courseClassID = $courseClass->id;

        $classLLOs = $courseClass->syllabus->lessonLearningOutcomes;
        if ($classLLOs->isEmpty()){
            $lloAchievements = null;
        } else {
            $classLLOsID = implode(',', $classLLOs->pluck('id')->toArray());
            $lloAchievements = DB::select("select llo.id, llo.code, llo.description, llop.n_passed_student, llop.llo_accomplishment, llop.total_student
                        from lesson_learning_outcomes llo
                        left join (select *, $totalStudentsCount as 'total_student', n_passed_student/$totalStudentsCount*100 as llo_accomplishment from (
                            select llo_id, code, description, count(llo_id) as n_passed_student from (
                                select *, student_point/max_llo_point*100 as lesson_outcome_accomplishment from (
                                    select sg.student_user_id, c.llo_id, llo.code, llo.description,
                                    sum(cl.`point`) as student_point, sum(c.max_point) as max_llo_point
                                    from student_grades sg
                                    join student_grade_details sgd on sgd.student_grade_id = sg.id
                                    join criteria_levels cl on cl.id = sgd.criteria_level_id
                                    join criterias c on c.id = cl.criteria_id
                                    join lesson_learning_outcomes llo on llo.id = c.llo_id
                                    join `assignments` a on a.id = sg.assignment_id
                                    where a.course_class_id = $courseClassID
                                    group by sg.student_user_id, c.llo_id
                                ) t1
                            ) t2 where lesson_outcome_accomplishment > $llo_threshold
                            group by llo_id
                        ) t3) llop on llo.id = llop.llo_id
                        where llo.id in ($classLLOsID)");
        }

        return view('class-portofolio.index', [
            'courseClass' => $courseClass,
            'lloAchievements' => collect($lloAchievements),
            'lloThreshold' => $llo_threshold,
            'totalStudentsCount' => $totalStudentsCount
        ]);
    }

    public function student(CourseClass $courseClass)
    {
        $this->authorize('view', [CourseClass::class, $courseClass]);

        $courseClass->load('students.studentData',
            'students.studentGrades.studentGradeDetails.criteriaLevel',
            'syllabus.lessonLearningOutcomes.criterias');

        $dataReturn = collect();
        $llos = $courseClass->syllabus->lessonLearningOutcomes;
        foreach ($courseClass->students as $student) {
            $nim = $student->studentData->student_id_number;
            $name = $student->name;
            $cpmk = collect();

            foreach ($llos as $llo) {
                $collectedPointPerLLO = 0;
                $maximumCollectiblePointPerLLO = 0;
                foreach ($llo->criterias as $criteria) {
                    foreach ($student->studentGrades as $studentGrade) {
                        foreach ($studentGrade->studentGradeDetails as $studentGradeDetail) {
                            if ($studentGradeDetail->criteriaLevel->criteria_id == $criteria->id) {
                                $collectedPointPerLLO += $studentGradeDetail->criteriaLevel->point;
                            }
                        }
                    }
                    $maximumCollectiblePointPerLLO += $criteria->max_point;
                }
                $cpmk->push(['point' => $collectedPointPerLLO, 'maxPoint' => $maximumCollectiblePointPerLLO]);
            }
            $dataReturn->push(['name' => $name, 'nim' => $nim, 'cpmk' => $cpmk]);
        }

        return view('class-portofolio.student', [
            'cc' => $courseClass,
            'llos' => $llos,
            'userData' => $dataReturn,
        ]);
    }
}
