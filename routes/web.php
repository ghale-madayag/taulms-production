<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\MeetController;
use App\Http\Controllers\ReportGradeController;
use App\Http\Controllers\ScreenRecordController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\DatabaseResetController;
use App\Models\User;
use Inertia\Inertia;
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

Route::middleware('auth')->group(function(){

    Route::controller(SessionController::class)->group(function(){
        Route::get('/', 'dashboard');

        Route::get('/faculty/create', 'create')->middleware(['permission:add email']);
        Route::get('/faculty/{faculty}/email', 'facultyEmail')->middleware(['permission:add email']);

        Route::get('/student/create', 'create')->middleware(['permission:add email']);

        Route::post('reset_password_without_token', 'validatePasswordRequest');
        Route::post('reset_password_with_token', 'resetPasswordRequest');
        Route::post('store','store');
    });

    Route::controller(SubjectController::class)->group(function(){
        Route::get('/subject','index')->middleware(['permission:view subject']);
        Route::get('/subject/all','all')->middleware(['permission:view all subject']);
        Route::get('/subject/{subject:slug}/{section}','show')->middleware(['permission:view subject']);
        Route::get('/report', 'report')->middleware(['permission:view student report']);
        Route::get('/course/{subject:slug}/{section}','course')->middleware(['permission:view subject']);
        //new route
        Route::get('/subject/{subject:slug}/{section}/{user}','showAll')->middleware(['permission:view all subject']);
        Route::post('/subject/{subjects}/update','update')->middleware(['permission:create lesson']);
        Route::post('/subject/delete/{subject}', 'destroy')->middleware(['permission:view all subject']);
    });

    Route::controller(StudentController::class)->group(function(){
        Route::get('/student/{student}/edit', 'edit');
        Route::get('/student/password','changePassword')->can('viewAny', User::class);
        Route::get('/student', 'index')->can('view', User::class);
        Route::get('/student/{id}', 'show')->can('view', User::class);
        Route::get('/student/{subject:slug}/{section}', 'viewStudents')->middleware(['permission:view students']);
        Route::get('/student/{subject:slug}/{section}/{user}', 'viewAllStudents')->middleware(['permission:view all students']);
    });

    Route::controller(ReportGradeController::class)->group(function(){
        Route::get('/grades/{subject:slug}/{section}/{term}/{cat}/{tabs}', 'index')->middleware('permission:manage grade');
        Route::get('/grades/{subject:slug}/{section}/{user}/', 'index')->middleware('permission:manage grade');
        Route::get('/criteria/{subject:slug}/{section}/{term}/criteria', 'criteria')->middleware('permission:manage grade');

        Route::post('/grade/upsert','upsert');
        Route::post('/criteria/category','category');
        Route::post('/criteria/mark','mark');
        Route::post('/criteria/subcategory','subcategory');
        Route::post('/criteria/subcategory/{student_grade_sub}/edit','editSub');
        Route::post('/criteria/category/{student_grade_cat}/edit','editCat');

        Route::put('/criteria/sort', 'sort');

        Route::delete('/criteria/subcategory/{student_grade_sub}','destroySub');
        Route::delete('/criteria/category/{student_grade_cat}','destroyCat');

    });

    Route::controller(FacultyController::class)->group(function(){
        Route::get('/faculty', 'index');
        Route::get('/faculty/{faculty}/edit', 'edit');
        Route::get('/faculty/password','changePassword');
    });

    Route::controller(LessonController::class)->group(function(){
        Route::get('/lesson/{subject:slug}/create','createWithSubject');
        Route::get('/lesson/{lesson:slug}/edit','edit')->middleware(['permission:edit lesson']);
       
        Route::get('/subject/{subject:slug}/lesson/{lesson:slug}/term/{term}/{section:id}', 'take');
        //new route
        Route::get('/lesson/{subject:slug}/{section}/{user}/create','createAll')->middleware(['permission:create all lesson']);
        Route::get('/lesson/{subject:slug}/{section}/create','create')->middleware(['permission:create lesson']);
        Route::get('/lesson/{lesson:slug}/edit/{user}','editAll')->middleware(['permission:view all lesson']);
        Route::get('/lesson/all', 'showAll')->middleware(['permission:view all subject']);
        Route::get('/lesson/{subject:slug}/{section}/', 'index')->middleware(['permission:view lesson dashboard']);
        Route::get('/lesson/{subject:slug}/{section}/{user}', 'listView')->middleware(['permission:view all lesson']);

        Route::put('/lesson/sort','sort');
        Route::put('/lesson/ismidterm/{lesson}', 'ismidterm');
        Route::put('/lesson/isfinals/{lesson}', 'isfinals');
        Route::post('/lesson/store','store');
        Route::post('/lesson/{lesson}/update','update');
        Route::delete('/lesson/{lesson}','destroy');
        Route::delete('/lesson/list/{lesson}','destroyList');
        //new route
        Route::delete('/lesson/{subject:slug}/{section}/{lesson}','destroyLesson');

        Route::post('/lesson/{lesson:slug}/active','active');
        Route::post('/lesson/{lesson:slug}/finished','finished');
        Route::post('/lesson/{subject:slug}/start','start');
        Route::post('/lesson/{subject:slug}/getActive','getActive');
        Route::post('/lesson/getSection/{id}','getSection');
        Route::post('/lesson/getSectionAll/{id}/{user}','getSectionAll');
        Route::post('/lesson/duplicate','duplicate');
    });

    Route::controller(QuizController::class)->group(function(){
        
        Route::get('/quiz/{quiz:slug}/question/{question}','show');
        Route::get('/quiz/{quiz:slug}/edit','edit')->middleware(['permission:edit quiz']);
        Route::get('/quiz/all', 'showAll')->middleware(['permission:create all quiz']);
        //new route
        Route::get('/quiz/{subject:slug}/{section}/create','create')->middleware(['permission:create quiz']);
        Route::get('/quiz/{subject:slug}/{section}/{user}/create','createAll')->middleware(['permission:create all quiz']);
        Route::get('/quiz/{subject:slug}/{section}/', 'index')->middleware(['permission:view quiz dashboard']);
        Route::get('/quiz/{subject:slug}/{section}/{user}', 'indexAll')->middleware(['permission:create all quiz']);
        Route::get('/review/{studentquiz}/quiz','review');
        Route::get('/faculty/review/{quiz:slug}/','faculty_review')->middleware(['permission:review quiz']);
        Route::get('/faculty/review/{quiz:slug}/{question}','actual_review')->middleware(['permission:review quiz']);
       

        Route::post('/quiz/store','store');
        Route::post('/quiz/{quiz}/update','update');
        Route::delete('/quiz/{quiz}','destroy');
        Route::delete('/quiz/list/{quiz}','destroyList');

        Route::post('/quiz/{quiz}/duplicate','duplicate');

    });

    Route::controller(QuestionController::class)->group(function(){
       
        Route::post('/question/{quiz:slug}/start','start');
        Route::post('/question/{question}/finished','finished');
        Route::post('/question/{question}/prev','prev');
        Route::post('/question/{question}/active','active');
        Route::post('/question/{question}/savescore','savescore');
    });

    Route::controller(AnnouncementController::class)->group(function (){
        Route::get('/announcement/{subject:slug}/{section}/create', 'create')->middleware(['permission:create announcement']);
        Route::get('/announcement/{announcement:slug}/edit','edit')->middleware(['permission:edit announcement']);
        Route::get('/announcement/{subject:slug}/{section}', 'index')->middleware(['permission:view announcement dashboard']);
        Route::get('/announcement/{subject:slug}/{section}/{user}', 'listView')->middleware(['permission:create all announcement']);        
        Route::get('/announcement/{subject:slug}/{section}/{user}/create', 'createAll')->middleware(['permission:create all announcement']);

        Route::post('/announcement/store', 'store');
        Route::post('/announcement/{announcement}/update','update');
        Route::delete('/announcement/list/{lesson}','destroy');
    });

    Route::controller(ScreenRecordController::class)->group(function (){
        Route::get('/recorder/getSubject', 'getSubject');
        Route::get('/recorder', 'index')->middleware(['permission:add time records']);
        Route::get('/recorder/{user}', 'indexAll')->middleware(['permission:view all time records']);
        
        Route::post('/recorder/{screenrecord}/data', 'store');
        Route::post('/recorder/start', 'startRecording');
        Route::post('/recorder/{screenrecord}/update', 'update');
        Route::delete('/recorder/destroy/{screenrecord}', 'destroy');
    });

    Route::controller(MeetController::class)->group(function(){
        Route::get('/meet/{subject:slug}/{section}/', 'index')->middleware(['permission:view conference dashboard']);
        Route::get('/meet/{subject:slug}/{section}/{user}', 'indexAll')->middleware(['permission:create all conference']);
        Route::post('/meet/store', 'store');
        Route::get('/meet/getEvent', 'getEvent');
        Route::delete('/meet/{meet}','destroy');
    });

    Route::controller(RolesController::class)->group(function(){
        Route::middleware(['auth', 'permission:manage roles'])->group(function () {
            Route::get('/roles', 'index');
            Route::get('/roles/create', 'create');
            Route::get('/roles/{role}/edit', 'edit');
        });
        
        Route::post('/roles/{role}/user/{user}', 'update');
        Route::post('/roles/store', 'store');
        Route::post('/roles/{role}/edit-role', 'editRole');
        Route::delete('/roles/{role}','destroy');
        
    });

    Route::controller(DatabaseResetController::class)->group(function(){
        Route::middleware(['auth', 'permission:manage database'])->group(function () {
            Route::get('/maintenance', 'index');

            Route::post('/migrate/reset-database', 'reset');
            Route::post('/migrate/maintenance', 'maintenance');
            Route::post('/migrate/maintenance/term', 'term');

        });
    });

    Route::post('/logout', function () {
        auth()->logout();
        return redirect()->route('login');
    });

});

Route::get('/forgot-password',function (){
    return Inertia::render('Session/ForgotPassword');
});

Route::get('/reset-password',function (){
    return Inertia::render('Session/ResetPassword');
});
