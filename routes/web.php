<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AssignmentPlanController;
use App\Http\Controllers\AssignmentPlanTasksController;
use App\Http\Controllers\ClassMemberController;
use App\Http\Controllers\ClassPortofolioController;
use App\Http\Controllers\ClassSettingController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\OfferCoursesController;
use App\Http\Controllers\TeacherCoursesController;
use App\Http\Controllers\AssignCoursesController;
use App\Http\Controllers\CourseLearningOutcomeController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\CriteriaLevelController;
use App\Http\Controllers\IntendedLearningOutcomeController;
use App\Http\Controllers\LearningPlanController;
use App\Http\Controllers\LessonLearningOutcomeController;
use App\Http\Controllers\MyGradeController;
use App\Http\Controllers\RubricController;
use App\Http\Controllers\StudentGradeController;
use App\Http\Controllers\SyllabusController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ProgramOutcomeController;
use App\Http\Controllers\CourseOutcomeController;
use App\Http\Controllers\CourseAssessmentController;
use App\Http\Controllers\CourseAttainmentController;

use App\Jobs\SendEmailJob;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// All authenticated and verified users
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::singleton('profile', ProfileController::class)->creatable();

    // All users with profile completed
    Route::group(['middleware' => ['profileCompleted']], function () {
        // Admin role
        Route::group(['middleware' => ['roles:admin']], function (){
            Route::resource('users', UserController::class);
            // Route::resource('schools', SchoolController::class);
            Route::resource('departments', DepartmentController::class);
            Route::resource('programs', ProgramController::class);
            Route::resource('courses', CourseController::class);
            Route::resource('offer_courses', OfferCoursesController::class);

            Route::get('/assign_courses', [AssignCoursesController::class, 'index'])->name('assign_courses.index');
            Route::get('/assign_courses/create', [AssignCoursesController::class, 'create'])->name('assign_courses.create');
            Route::post('/assign_courses/store', [AssignCoursesController::class, 'store'])->name('assign_courses.store');

            // Primary Teacher Edit and Unassign Routes
            Route::get('/assign_courses/{id}/edit_primary', [AssignCoursesController::class, 'edit_primary'])->name('assign_courses.edit_primary');
            Route::patch('/assign_courses/{id}/update_primary', [AssignCoursesController::class, 'update_primary'])->name('assign_courses.update_primary');
            Route::delete('/assign_courses/{id}/unassign_primary', [AssignCoursesController::class, 'unassign_primary'])->name('assign_courses.unassign_primary');

            // Secondary Teacher Edit and Unassign Routes
            Route::get('/assign_courses/{course_id}/edit_secondary/{teacher_id}', [AssignCoursesController::class, 'edit_secondary'])->name('assign_courses.edit_secondary');
            Route::patch('/assign_courses/{id}/update_secondary', [AssignCoursesController::class, 'update_secondary'])->name('assign_courses.update_secondary');
            Route::delete('/assign_courses/{id}/unassign_secondary', [AssignCoursesController::class, 'unassign_secondary'])->name('assign_courses.unassign_secondary');
            

            Route::resource('students', StudentController::class);
            Route::resource('faculties', FacultyController::class);
            
            Route::resource('program_outcomes', ProgramOutcomeController::class);
            

            // Route::get('/programs/{program}/edit', 'ProgramController@edit')->name('programs.edit');
            // Route::delete('/programs/{program}', 'ProgramController@destroy')->name('programs.destroy');
            
            Route::scopeBindings()->group(function () {
                Route::resource('schools.departments', DepartmentController::class);
                Route::resource('departments.programs', ProgramController::class);
                Route::resource('programs.courses', CourseController::class);
            });
// Route::group(['middleware' => ['auth', 'verified']], function () {
//     Route::singleton('profile', ProfileController::class)->creatable();

//     // All users with profile completed
//     Route::group(['middleware' => ['profileCompleted']], function () {
//         // Admin role
//         Route::group(['middleware' => ['roles:admin']], function (){
//             Route::resource('users', UserController::class);
//             Route::resource('faculties', FacultyController::class);
//             Route::resource('courses', CourseController::class);

//             Route::scopeBindings()->group(function () {
//                 Route::resource('faculties.departments', DepartmentController::class);
//                 Route::resource('faculties.departments.programs', ProgramController::class);
//             });
        });

        // Teacher or admin roles
        Route::group(['middleware' => ['roles:admin,teacher']], function (){
            Route::resource('syllabi', SyllabusController::class);
            Route::resource('rubrics', RubricController::class);
            Route::resource('assessments', AssessmentController::class);
            Route::resource('teacher_courses', TeacherCoursesController::class);

            Route::get('/course_outcomes', [CourseOutcomeController::class, 'index'])->name('course_outcomes.index');
            // Route::get('/course_outcomes/create', [CourseOutcomeController::class, 'create'])->name('course_outcomes.create');
            Route::post('/course_outcomes/{course}/store', [CourseOutcomeController::class, 'store'])->name('course_outcomes.store');
            Route::get('/course_outcomes/{course}', [CourseOutcomeController::class, 'show'])->name('course_outcomes.show');
            Route::get('/course_outcomes/{course}/{course_outcome}/edit', [CourseOutcomeController::class, 'edit'])->name('course_outcomes.edit');
            Route::put('/course_outcomes/{course}/{course_outcome}/update', [CourseOutcomeController::class, 'update'])->name('course_outcomes.update');
            Route::delete('/course_outcomes/{course}/{course_outcome}/destroy', [CourseOutcomeController::class, 'destroy'])->name('course_outcomes.destroy');
            
            Route::get('/course_outcomes/{course}/add_outcomes', [CourseOutcomeController::class, 'add_outcomes'])->name('course_outcomes.add_outcomes');
            Route::get('/course_outcomes/{course}/add_outcomes/create', [CourseOutcomeController::class, 'create'])->name('course_outcomes.create');
            Route::get('/course_outcomes/{course}/map_outcomes', [CourseOutcomeController::class, 'map_outcomes'])->name('course_outcomes.map_outcomes');
            Route::post('/course_outcomes/{course}/map_outcomes/save_mapping', [CourseOutcomeController::class, 'save_mapping'])->name('course_outcomes.save_mapping');
            Route::post('/course_outcomes/{course_outcome}/add_outcomes/update_status', [CourseOutcomeController::class, 'update_status'])->name('course_outcomes.update_status');
            Route::get('/course_outcomes/{course}/course_assessments', [CourseOutcomeController::class, 'course_assessments'])->name('course_outcomes.course_assessments');
            
            Route::get('/course_outcomes/{course}/question_outcome', [CourseOutcomeController::class, 'question_outcome'])->name('course_outcomes.question_outcome');
            Route::post('/course_outcomes/{course_assessment_id}/question_outcome/store', [CourseOutcomeController::class, 'question_outcome_store'])->name('course_outcomes.question_outcome_store');
            Route::get('/course_outcomes/question_outcome/get_assessment_details', [CourseOutcomeController::class, 'get_assessment_details'])->name('course_outcomes.get_assessment_details');

            Route::get('/course_outcomes/{course}/student_marks', [CourseOutcomeController::class, 'student_marks'])->name('course_outcomes.student_marks');
            Route::post('/course_outcomes/student_marks_store', [CourseOutcomeController::class, 'student_marks_store'])->name('course_outcomes.student_marks_store');
            Route::get('/course_outcomes/student_marks/fetch_existing_marks', [CourseOutcomeController::class, 'fetch_existing_marks'])->name('course_outcomes.fetch_existing_marks');

            Route::post('/course_assessments', [CourseAssessmentController::class, 'store'])->name('course_assessments.store');
            Route::put('/course_assessments/{id}', [CourseAssessmentController::class, 'update'])->name('course_assessments.update');
            Route::delete('/course_assessments/{id}', [CourseAssessmentController::class, 'destroy'])->name('course_assessments.destroy');
            Route::get('/course_assessments/{assessment_id}/{course_id}', [CourseAssessmentController::class, 'getCourseAssessment']);
            Route::get('/course_assessments/course/assessment_data/{course_id}', [CourseAssessmentController::class, 'getCourseAssessmentsForCourse']);
            
            Route::get('/get_departments/{school_id}', [ProgramController::class, 'get_departments']);
            Route::get('/get_programs/{department_id}', [ProgramController::class, 'get_programs']);
            // Route::get('/get_courses/{program_id}', [ProgramController::class, 'get_courses']);
            Route::get('/get_courses/{program_id}/{semester?}', [ProgramController::class, 'get_courses']);
            
            //Course Attainment
            Route::get('/course_attainments', [CourseAttainmentController::class, 'index'])->name('course_attainments.index');
            Route::get('/course_attainments/{course}/attainment_index', [CourseAttainmentController::class, 'attainment_index'])->name('course_attainments.attainment_index');

            Route::get('class-portofolio/{courseClass}', [ClassPortofolioController::class, 'index'])->name('class-portofolio.index');
            Route::get('class-portofolio/{courseClass}/students', [ClassPortofolioController::class, 'student'])->name('class-portofolio.student');

            Route::scopeBindings()->group(function () {
                Route::resource('syllabi.ilos', IntendedLearningOutcomeController::class);
                Route::resource('syllabi.clos', CourseLearningOutcomeController::class);
                Route::resource('syllabi.llos', LessonLearningOutcomeController::class);
                Route::resource('syllabi.learning-plans', LearningPlanController::class);
                Route::resource('syllabi.assignment-plans', AssignmentPlanController::class);
                Route::resource('syllabi.assignment-plans.assignment-plan-tasks', AssignmentPlanTasksController::class);
                Route::resource('rubrics.criterias', CriteriaController::class);
                Route::resource('rubrics.criterias.criteria-levels', CriteriaLevelController::class);
                Route::resource('classes.assignments', AssignmentController::class);
                Route::resource('classes.assignments.student-grades', StudentGradeController::class);

                Route::singleton('classes.setting', ClassSettingController::class);
                Route::singleton('classes.members', ClassMemberController::class)->creatable();
            });
        });

        // Student role
        Route::group(['middleware' => ['roles:student']], function (){
            Route::post('classes/join/process', [CourseClassController::class, 'join'])->name('classes.join');
            Route::get('classes/join',[CourseClassController::class, 'show_join'])->name('classes.show_join');

            Route::get('myGrade', [MyGradeController::class, 'index'])->name('mygrade.index');
            Route::get('myGrade/{courseClass}', [MyGradeController::class, 'show'])->name('mygrade.show');
        });

        // All roles
        Route::get('home', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('syllabi/{syllabus}', [SyllabusController::class, 'show'])->name('syllabi.show');
        Route::resource('classes', CourseClassController::class);
        Route::get('classes/{class}/assignments/{assignment}', [AssignmentController::class, 'show'])
            ->name('classes.assignments.show')->scopeBindings();
    });
});

// Route::get('send-email-queue', function(){
//     $details['otp'] =4556;
//     $details['name'] ="jayant";
//     $details['email'] = 'jayantsinghjhala@gmail.com';
//     dispatch(new SendEmailJob($details));
//     return response()->json(['message'=>'Mail Send Successfully!!']);
// });

require __DIR__ . '/auth.php';
