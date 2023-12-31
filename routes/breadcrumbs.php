<?php
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Str;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('dashboard'));
});

Breadcrumbs::for('users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Users', route('users.index'));
});

Breadcrumbs::for('users.create', function (BreadcrumbTrail $trail) {
    $trail->parent('users.index');
    $trail->push('Create');
});

Breadcrumbs::for('users.edit', function (BreadcrumbTrail $trail,$user) {
    $trail->parent('users.index');
    $trail->push($user->name, route('departments.edit',$user));
});

Breadcrumbs::for('users.show', function (BreadcrumbTrail $trail,$user) {
    $trail->parent('users.index');
    $trail->push($user->name, route('departments.show',$user));
});

// Faculties
Breadcrumbs::for('faculties.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Faculties', route('faculties.index'));
});

// Faculty Create
Breadcrumbs::for('faculties.create', function (BreadcrumbTrail $trail) {
    $trail->parent('faculties.index');
    $trail->push('Create', route('faculties.create'));
});

// Faculty Edit
Breadcrumbs::for('faculties.edit', function (BreadcrumbTrail $trail, $faculty) {
    $trail->parent('faculties.index');
    $trail->push($faculty->name, route('faculties.edit', $faculty));
});

// Departments
Breadcrumbs::for('departments.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Departments', route('departments.index'));
});

// Department Create
Breadcrumbs::for('departments.create', function (BreadcrumbTrail $trail) {
    $trail->parent('departments.index');
    $trail->push('Create');
});

// Department Edit
Breadcrumbs::for('departments.edit', function (BreadcrumbTrail $trail,$department) {
    $trail->parent('departments.index');
    $trail->push($department->name, route('departments.edit',$department));
});

// Programs
Breadcrumbs::for('programs.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Programs', route('programs.index'));
});

// Department Create
Breadcrumbs::for('programs.create', function (BreadcrumbTrail $trail) {
    $trail->parent('programs.index');
    $trail->push('Create');
});

// Department Edit
Breadcrumbs::for('programs.edit', function (BreadcrumbTrail $trail,$program) {
    $trail->parent('programs.index');
    $trail->push($program->name, route('programs.edit',$program));
});

//Offer Courses
Breadcrumbs::for('offer_courses.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Offer Courses', route('offer_courses.index'));
});

Breadcrumbs::for('offer_courses.create', function (BreadcrumbTrail $trail) {
    $trail->parent('offer_courses.index');
    $trail->push('Offer');
});

Breadcrumbs::for('offer_courses.edit', function (BreadcrumbTrail $trail,$course) {
    $trail->parent('offer_courses.index');
    $trail->push($course->name, route('offer_courses.edit',$course));
});

//Assign Courses
Breadcrumbs::for('assign_courses.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Assign Courses', route('assign_courses.index'));
});

Breadcrumbs::for('assign_courses.create', function (BreadcrumbTrail $trail) {
    $trail->parent('assign_courses.index');
    $trail->push('Assign');
});

Breadcrumbs::for('assign_courses.edit', function (BreadcrumbTrail $trail,$course) {
    $trail->parent('assign_courses.index');
    $trail->push($course->name);
});

//Students
Breadcrumbs::for('students.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Students', route('students.index'));
});

Breadcrumbs::for('students.create', function (BreadcrumbTrail $trail) {
    $trail->parent('students.index');
    $trail->push('Create');
});

Breadcrumbs::for('students.edit', function (BreadcrumbTrail $trail,$student) {
    $trail->parent('students.index');
    $trail->push($student->student_name);
});

//Assessment
Breadcrumbs::for('assessments.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Assessments', route('assessments.index'));
});

Breadcrumbs::for('assessments.create', function (BreadcrumbTrail $trail) {
    $trail->parent('assessments.index');
    $trail->push('Create');
});

Breadcrumbs::for('assessments.edit', function (BreadcrumbTrail $trail,$assessment) {
    $trail->parent('assessments.index');
    $trail->push($assessment->assessment_name);
});

//Program Outcomes
Breadcrumbs::for('program_outcomes.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Program Outcomes', route('program_outcomes.index'));
});

Breadcrumbs::for('program_outcomes.create', function (BreadcrumbTrail $trail) {
    $trail->parent('program_outcomes.index');
    $trail->push('Create');
});

Breadcrumbs::for('program_outcomes.edit', function (BreadcrumbTrail $trail,$program_outcome) {
    $trail->parent('program_outcomes.index');
    $trail->push($program_outcome->name, route('program_outcomes.edit',$program_outcome));
});

//Teacher Courses
Breadcrumbs::for('teacher_courses.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Courses', route('teacher_courses.index'));
});

//Teacher Courses > Show
Breadcrumbs::for('teacher_courses.show', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('home');
    $trail->push('Courses', route('teacher_courses.index'));
    $trail->push($course->name);
});

//Teacher Courses > Create
Breadcrumbs::for('teacher_courses.create', function (BreadcrumbTrail $trail) {
    $trail->parent('teacher_courses.index');
    $trail->push('Create', route('teacher_courses.create'));
});

//Teacher Courses > Edit
Breadcrumbs::for('teacher_courses.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Courses', route('teacher_courses.index'));
    $trail->push('Edit', route('teacher_courses.edit'));
});

//course_outcomes courses
Breadcrumbs::for('course_outcomes.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Courses', route('course_outcomes.index'));
});

//course_outcomes index
Breadcrumbs::for('course_outcomes.co_index', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('course_outcomes.index');
    $trail->push($course->name,route('course_outcomes.add_outcomes', $course));
});

//course_outcomes > Show
Breadcrumbs::for('course_outcomes.show', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('home');
    $trail->push('Courses', route('course_outcomes.index'));
    $trail->push($course->name);
});

//course_outcomes > Create
Breadcrumbs::for('course_outcomes.create', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('course_outcomes.index');
    $trail->push($course->name, route('course_outcomes.add_outcomes', $course));
    $trail->push('Create');
});

//course_outcomes> Edit
Breadcrumbs::for('course_outcomes.edit', function (BreadcrumbTrail $trail, $course, $course_outcome) {
    $trail->parent('course_outcomes.co_index',$course);
    $trail->push($course_outcome->name);
    $trail->push('Edit');
});

Breadcrumbs::for('course_outcomes.map_co_index', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('course_outcomes.index');
    $trail->push($course->name);
    $trail->push("Map Course and Program Outcomes");
});

Breadcrumbs::for('course_outcomes.course_assessments', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('course_outcomes.index');
    $trail->push($course->name);
    $trail->push("Select Course Assessments");
});

Breadcrumbs::for('course_outcomes.question_outcome', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('course_outcomes.index');
    $trail->push($course->name);
    $trail->push("Assign Questions Marks");
});

Breadcrumbs::for('course_outcomes.student_marks', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('course_outcomes.index');
    $trail->push($course->name);
    $trail->push("Assign Student Marks");
});

// Syllabi
Breadcrumbs::for('syllabi.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Syllabi', route('syllabi.index'));
});

//Course Attainent
//course_outcomes courses
Breadcrumbs::for('course_attainments.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Courses', route('course_attainments.index'));
});

Breadcrumbs::for('course_attainments.attainment_index', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('course_outcomes.index');
    $trail->push($course->name,route('course_attainments.attainment_index', $course));
});

// Syllabi > Show
Breadcrumbs::for('syllabi.show', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('home');
    $trail->push('Syllabi', route('syllabi.index'));
    $trail->push($syllabus->title, route('syllabi.show', $syllabus));
});

// Syllabi > Create
Breadcrumbs::for('syllabi.create', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Syllabi', route('syllabi.index'));
    $trail->push('Create', route('syllabi.create'));
});

// Syllabi > Edit
Breadcrumbs::for('syllabi.edit', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('home');
    $trail->push('Syllabi', route('syllabi.index'));
    $trail->push($syllabus->title, route('syllabi.show', $syllabus));
    $trail->push('Edit', route('syllabi.edit', $syllabus));
});

// Learning PLans
Breadcrumbs::for('learning-plans.index', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('home');
    $trail->push($syllabus->title, route('syllabi.show', $syllabus));
    $trail->push('Learning Plans', route('syllabi.learning-plans.index', $syllabus));
});

// Learning PLans > Create
Breadcrumbs::for('learning-plans.create', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('learning-plans.index', $syllabus);
    $trail->push('Create', route('syllabi.learning-plans.create', $syllabus));
});

// Learning PLans > Edit
Breadcrumbs::for('learning-plans.edit', function (BreadcrumbTrail $trail, $syllabus, $learningPlan) {
    $trail->parent('learning-plans.index', $syllabus);
    $trail->push("Edit", route('syllabi.learning-plans.edit', [$syllabus, $learningPlan]));
});

// For all children of syllabi
Breadcrumbs::for('syllabi.*', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('home');
    $trail->push("Syllabi", route('syllabi.index'));
    $trail->push($syllabus->title, route('syllabi.show', $syllabus));
});

// Assignment Plan Tasks > Create
Breadcrumbs::for('assignment-plan-tasks.create', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('syllabi.*', $syllabus);
    $trail->push('Create New Assignment Plan Task');
});

// Assignment Plan Tasks > Edit
Breadcrumbs::for('assignment-plan-tasks.edit', function (BreadcrumbTrail $trail, $syllabus, $assignmentPlanTask) {
    $trail->parent('syllabi.*', $syllabus);
    $trail->push(Str::limit($assignmentPlanTask->description, 30));
});

// Assignment Plans
Breadcrumbs::for('assignment-plans.index', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('home');
    $trail->push("Syllabi", route('syllabi.index'));
    $trail->push($syllabus->title, route('syllabi.show', $syllabus));
});

// Assignment Plans > Create
Breadcrumbs::for('assignment-plans.create', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('assignment-plans.index', $syllabus);
    $trail->push('Create Assignment Plan', route('syllabi.assignment-plans.create', $syllabus));
});

// Assignment Plans > Edit
Breadcrumbs::for('assignment-plans.edit', function (BreadcrumbTrail $trail, $syllabus, $assignmentPlan) {
    $trail->parent('assignment-plans.index', $syllabus);
    $trail->push("Edit Assignment Plan", route('syllabi.assignment-plans.edit', [$syllabus, $assignmentPlan]));
});

// Learing Outcomes
Breadcrumbs::for('learning-outcomes.index', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('home');
    $trail->push('Syllabi', route('syllabi.index'));
    $trail->push($syllabus->title, route('syllabi.show', $syllabus));
});

// ILOs > Create
Breadcrumbs::for('ilos.create', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('learning-outcomes.index', $syllabus);
    $trail->push('Create ILO');
});

// ILOs > Edit
Breadcrumbs::for('ilos.edit', function (BreadcrumbTrail $trail, $syllabus, $ilo) {
    $trail->parent('learning-outcomes.index', $syllabus);
    $trail->push(Str::limit($ilo->description, 30));
});

// CLOs > Create
Breadcrumbs::for('clos.create', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('learning-outcomes.index', $syllabus);
    $trail->push('Create CLO');
});

// CLOs > Edit
Breadcrumbs::for('clos.edit', function (BreadcrumbTrail $trail, $syllabus, $clo) {
    $trail->parent('learning-outcomes.index', $syllabus);
    $trail->push(Str::limit($clo->description, 30));
});

// LLOs > Create
Breadcrumbs::for('llos.create', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('learning-outcomes.index', $syllabus);
    $trail->push('Create LLO');
});

// LLOs > Edit
Breadcrumbs::for('llos.edit', function (BreadcrumbTrail $trail, $syllabus, $llo) {
    $trail->parent('learning-outcomes.index', $syllabus);
    $trail->push(Str::limit($llo->description, 30));
});

// Courses
Breadcrumbs::for('courses.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Courses', route('courses.index'));
});

// Courses > Show
Breadcrumbs::for('courses.show', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('home');
    $trail->push('Courses', route('courses.index'));
    $trail->push($course->name, route('courses.show', $course));
});

// Courses > Create
Breadcrumbs::for('courses.create', function (BreadcrumbTrail $trail) {
    $trail->parent('courses.index');
    $trail->push('Create', route('courses.create'));
});

// Courses > Edit
Breadcrumbs::for('courses.edit', function (BreadcrumbTrail $trail, $course) {
    $trail->parent('home');
    $trail->push('Courses', route('courses.index'));
    $trail->push($course->name, route('courses.show', $course));
    $trail->push('Edit', route('courses.edit', $course));
});

// Course classes
Breadcrumbs::for('classes.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Classes', route('classes.index'));
});

// Course classes > Create
Breadcrumbs::for('classes.create', function (BreadcrumbTrail $trail) {
    $trail->parent('classes.index');
    $trail->push('Create', route('classes.create'));
});

// Course classes > Edit
Breadcrumbs::for('classes.edit', function (BreadcrumbTrail $trail, $class) {
    $trail->parent('classes.index');
    $trail->push($class->name, route('classes.show', $class));
    $trail->push('Edit', route('classes.edit', $class));
});

// Course classes > Join
Breadcrumbs::for('classes.join', function (BreadcrumbTrail $trail) {
    $trail->parent('classes.index');
    $trail->push('Join', route('classes.join'));
});

// Course classes > Settings
Breadcrumbs::for('classes.settings', function (BreadcrumbTrail $trail, $class) {
    $trail->parent('classes.index');
    $trail->push($class->name, route('classes.show', $class));
    $trail->push('Settings');
});

// Classes members > Show
Breadcrumbs::for('class-members.show', function (BreadcrumbTrail $trail, $class) {
    $trail->parent('home');
    $trail->push($class->name, route('classes.show', $class));
    $trail->push('Members');
});

// Assignments > Create
Breadcrumbs::for('assignments.create', function (BreadcrumbTrail $trail, $class) {
    $trail->parent('home');
    $trail->push($class->name, route('classes.show', $class));
    $trail->push('Create Assignment', route('classes.assignments.create', $class));
});

// Assignments > Edit
Breadcrumbs::for('assignments.edit', function (BreadcrumbTrail $trail, $class, $assignment) {
    $trail->parent('home');
    $trail->push($class->name, route('classes.show', $class));
    $trail->push(Str::limit($assignment->assignmentPlan->title, 40) ?? "-", route('classes.assignments.show', [$class, $assignment]));
    $trail->push('Edit', route('classes.assignments.edit', [$class, $assignment]));
});

// Assignments > Show
Breadcrumbs::for('assignments.show', function (BreadcrumbTrail $trail, $class, $assignment) {
    $trail->parent('home');
    $trail->push($class->name, route('classes.show', $class));
    $trail->push(Str::limit($assignment->assignmentPlan->title, 40) ?? "-", route('classes.assignments.show', [$class, $assignment]));
});

// Rubrics
Breadcrumbs::for('rubrics.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Rubrics', route('rubrics.index'));
});

// Rubrics > Create
Breadcrumbs::for('rubrics.create', function (BreadcrumbTrail $trail, $syllabus) {
    $trail->parent('home');
    $trail->push(Str::limit($syllabus->title, 30), route('syllabi.show', $syllabus));
    $trail->push('Create', route('rubrics.create'));
});

// Rubrics > Show
Breadcrumbs::for('rubrics.show', function (BreadcrumbTrail $trail, $syllabus, $rubric) {
    $trail->parent('home');
    $trail->push(Str::limit($syllabus->title, 30), route('syllabi.show', $syllabus));
    $trail->push(Str::limit($rubric->title, 30), route('rubrics.show', $rubric));
});

// Criterias > Create
Breadcrumbs::for('criterias.create', function (BreadcrumbTrail $trail, $rubric) {
    $trail->parent('home');
    $trail->push(Str::limit($rubric->title, 30), route('rubrics.show', $rubric));
    $trail->push('Create Criteria');
});

// Criterias > Edit
Breadcrumbs::for('criterias.edit', function (BreadcrumbTrail $trail, $rubric, $criteria) {
    $trail->parent('home');
    $trail->push(Str::limit($rubric->title, 30), route('rubrics.show', $rubric));
    $trail->push(Str::limit($criteria->title, 30));
});

// Criteria Levels > Create
Breadcrumbs::for('criteria-levels.create', function (BreadcrumbTrail $trail, $rubric) {
    $trail->parent('home');
    $trail->push(Str::limit($rubric->title, 30), route('rubrics.show', $rubric));
    $trail->push('Create Criteria Level');
});

// Criteria Levels > Edit
Breadcrumbs::for('criteria-levels.edit', function (BreadcrumbTrail $trail, $rubric, $criteriaLevel) {
    $trail->parent('home');
    $trail->push(Str::limit($rubric->title, 30), route('rubrics.show', $rubric));
    $trail->push(Str::limit($criteriaLevel->title, 30));
});

// Student Grades
Breadcrumbs::for('student-grades.index', function (BreadcrumbTrail $trail, $class, $assignment) {
    $trail->parent('home');
    $trail->push(Str::limit($class->name, 30), route('classes.show', $class));
    $trail->push(Str::limit($assignment->assignmentPlan->title, 30), route('classes.assignments.show', [$class, $assignment]));
    $trail->push('Student Grades', route('classes.assignments.student-grades.index', [
        $class, $assignment
    ]));
});

// Student Grades > Edit
Breadcrumbs::for('student-grades.edit', function (BreadcrumbTrail $trail, $class, $assignment) {
    $trail->parent('student-grades.index', $class, $assignment);
    $trail->push('Edit');
});

// Student Grades > Create
Breadcrumbs::for('student-grades.create', function (BreadcrumbTrail $trail, $class, $assignment) {
    $trail->parent('student-grades.index', $class, $assignment);
    $trail->push('Create');
});

// Class Portfolios
Breadcrumbs::for('class-portofolio.index', function (BreadcrumbTrail $trail, $class) {
    $trail->parent('home');
    $trail->push(Str::limit($class->name, 30), route('classes.show', $class));
    $trail->push('LLO Portfolio', route('class-portofolio.index', $class));
});

// Class Portfolios > Student
Breadcrumbs::for('class-portofolio.student', function (BreadcrumbTrail $trail, $class) {
    $trail->parent('home');
    $trail->push(Str::limit($class->name, 30), route('classes.show', $class));
    $trail->push('Student Portfolio', route('class-portofolio.index', $class));
});

// MyGrade > Index
Breadcrumbs::for('mygrade.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('My Grades', route('mygrade.index'));
});

// MyGrade > Show
Breadcrumbs::for('mygrade.show', function (BreadcrumbTrail $trail, $class) {
    $trail->parent('mygrade.index');
    $trail->push(Str::limit($class->name, 30), route('mygrade.show', $class));
});
