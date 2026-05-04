<?php
// routes/web.php
use App\Http\Controllers\{
    DashboardController, CourseController, ModuleController,
    LessonController, QuizController, ReportController,
    UserController, ProfileController,
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));
require __DIR__.'/auth.php';

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/profile',          [ProfileController::class,'edit'])->name('profile.edit');
    Route::patch('/profile',        [ProfileController::class,'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class,'updatePassword'])->name('profile.password');

    Route::resource('courses', CourseController::class);
    Route::post('/courses/{course}/enroll',     [CourseController::class,'enroll'])->name('courses.enroll');
    Route::get('/courses/{course}/certificate', [CourseController::class,'certificate'])->name('courses.certificate');

    Route::post('/courses/{course}/modules',               [ModuleController::class,'store'])->name('modules.store');
    Route::delete('/courses/{course}/modules/{module}',    [ModuleController::class,'destroy'])->name('modules.destroy');

    Route::post('/courses/{course}/modules/{module}/lessons',  [LessonController::class,'store'])->name('lessons.store');
    Route::get('/courses/{course}/lessons/{lesson}',           [LessonController::class,'show'])->name('lessons.show');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [LessonController::class,'markComplete'])->name('lessons.complete');
    Route::delete('/courses/{course}/lessons/{lesson}',        [LessonController::class,'destroy'])->name('lessons.destroy');

    Route::get('/courses/{course}/quizzes/create',          [QuizController::class,'create'])->name('quizzes.create');
    Route::post('/courses/{course}/quizzes',                [QuizController::class,'store'])->name('quizzes.store');
    Route::get('/courses/{course}/quizzes/{quiz}',          [QuizController::class,'show'])->name('quizzes.show');
    Route::post('/courses/{course}/quizzes/{quiz}/submit',  [QuizController::class,'submit'])->name('quizzes.submit');
    Route::delete('/courses/{course}/quizzes/{quiz}',       [QuizController::class,'destroy'])->name('quizzes.destroy');

    Route::middleware('role:admin|instructor')->group(function () {
        Route::get('/reports',     [ReportController::class,'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class,'exportPdf'])->name('reports.pdf');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
