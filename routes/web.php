<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\HODDashboardController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\HODMiddleware;
use App\Http\Middleware\SupervisorMiddleware;
use App\Http\Middleware\StudentMiddleware;

Route::get('/', function () {
    return redirect('/login');
});

// // Login + Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Register
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/get-campus-departments/{campus_id}', [AuthController::class, 'getCampusDepartments']);
Route::get('/get-faculties-by-campus-department/{campus_department_id}', [AuthController::class, 'getFacultiesByCampusDepartment']);

// Protected Dashboard Routes
Route::middleware(['role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    // Add more student routes here
});

Route::middleware(['role:hod'])->group(function () {
    Route::get('/hod/dashboard', [HODDashboardController::class, 'index'])->name('hod.dashboard');
    // Add more HOD routes here
});

Route::middleware(['role:supervisor'])->group(function () {
    Route::get('/supervisor/dashboard', [SupervisorDashboardController::class, 'index'])->name('supervisor.dashboard');
    // Add more supervisor routes here
});
Route::get('/supervisor/project/{project_code}', [SupervisorDashboardController::class, 'viewProject'])
    ->name('supervisor.view.project');

// // Catch-all route for dashboard attempts without auth
// Route::get('/{any}', function () {
//     return redirect()->route('login');
// })->where('any', '.*')->middleware('web');

Route::post('/student/submit-project', [StudentDashboardController::class, 'submitProject'])
    ->name('student.submit-project')
    ->middleware('role:student');
Route::post('/hod/store-supervisor', [HODDashboardController::class, 'storeSupervisor'])
    ->name('hod.store-supervisor')
    ->middleware('role:hod');

Route::put('/hod/update-supervisor/{email}', [HODDashboardController::class, 'updateSupervisor'])->name('hod.update-supervisor');
Route::delete('/hod/delete-supervisor/{email}', [HODDashboardController::class, 'deleteSupervisor'])->name('hod.delete-supervisor');
Route::post('/hod/assign-supervisor', [HODDashboardController::class, 'assignSupervisor'])
    ->name('hod.assign-supervisor')
    ->middleware('role:hod');


Route::delete('/hod/unassign-supervisor', [HODDashboardController::class, 'unassignSupervisor'])
    ->name('hod.unassign-supervisor')
    ->middleware('role:hod');

Route::post('/hod/update-project-status', [HODDashboardController::class, 'updateProjectStatus'])
    ->name('hod.update-project-status')
    ->middleware('role:hod');

Route::delete('/student/project/delete', [StudentDashboardController::class, 'deleteProject'])
    ->name('student.project.delete');

Route::put('/student/project/update', [StudentDashboardController::class, 'updateProject'])->name('student.project.update');


// Admin authentication routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Optional: Admin registration routes
    // Route::get('/register', [AdminAuthController::class, 'showRegistrationForm'])->name('register');
    // Route::post('/register', [AdminAuthController::class, 'register'])->name('register.submit');
});

// Chat routes
Route::get('/chat/{project_code}/{receiver_email?}', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
Route::post('/chat/get-messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.getMessages');
// Protected admin routes

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.manage-campus');
    });

    Route::get('/manage-campus', [AdminController::class, 'manageCampus'])->name('manage-campus');
    Route::post('/campus/store', [AdminController::class, 'storeCampus'])->name('store-campus');

    Route::get('/manage-department', [AdminController::class, 'manageDepartment'])->name('manage-department');
    Route::post('/campus-department/store', [AdminController::class, 'storeCampusDepartment'])->name('store-campus-department');
    Route::post('/department/store', [AdminController::class, 'storeDepartment'])->name('store-department');

    Route::get('/get-faculties/{department_code}', [AdminController::class, 'getFacultiesByDepartment']);
    Route::get('/manage-faculty', [AdminController::class, 'manageFaculty'])->name('manage-faculty');
    Route::post('/faculty/store', [AdminController::class, 'storeFaculty'])->name('store-faculty');
    Route::post('/campus-faculty/store', [AdminController::class, 'storeCampusFaculty'])->name('store-campus-faculty');

    Route::get('/manage-hod', [AdminController::class, 'manageHOD'])->name('manage-hod');
    Route::post('/store-hod', [AdminController::class, 'storeHOD'])->name('store-hod');

    Route::get('/get-departments-by-campus/{campus_id}', [AdminController::class, 'getDepartmentsByCampus']);
});