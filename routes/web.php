<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserCreateController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\GoogleChatController;
use App\Http\Controllers\GoogleController;

Route::get('/', function () {
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| Dashboard Redirect (Jetstream Override)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/dashboard', function () {

    $user = auth()->user();

    return match ($user->role) {
        'super_admin' => redirect('/super-admin/dashboard'),
        'admin'       => redirect('/admin/dashboard'),
        default       => redirect('/user/dashboard'),
    };
})->name('dashboard');


/*
|--------------------------------------------------------------------------
| Common Task Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Attendance Modal Logic
    Route::post('/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/check-out', [AttendanceController::class, 'checkOut']);

    Route::get('/auth/facebook', [InstagramController::class, 'redirect']);
    Route::get('/auth/facebook/callback', [InstagramController::class, 'callback']);
    Route::get('/instagram/account/{id}', [InstagramController::class, 'post']);
    Route::post('/instagram/publish', [InstagramController::class, 'publish'])->name('instagram.publish');
    Route::delete('/instagram/delete/{id}', [InstagramController::class, 'deletePost']);
    Route::post('/instagram/publish/{id}', [InstagramController::class, 'publish']);
    // Task Assignment Routes
    Route::get('/task', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/tasks/update-status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::post('/tasks/update/{id}', [TaskController::class, 'update'])->name('tasks.update');

    // User Dashboard Routes
    Route::get('/tasks/counts', [UserController::class, 'taskCounts'])->name('tasks.counts');
    Route::post('/calendar/events/store', [UserController::class, 'calendarEventsStore'])->name('calendar.events.store');
    Route::post('/calendar/{id}/status', [UserController::class, 'updatePostStatus']);
    Route::get('/view-attendance', [UserController::class, 'viewAttendance'])->name('view-attendance');
    Route::post('/password-change', [UserController::class, 'updatePassword'])->middleware('auth')->name('password.change');


    Route::get('/user/apply-leave', [LeaveRequestController::class, 'create'])->name('leave.apply');
    Route::post('/user/apply-leave', [LeaveRequestController::class, 'store'])->name('leave.store');


    // sticky-notes routes

    Route::get('/sticky-notes', [NoteController::class, 'index'])->name('sticky-notes.index');
    Route::post('/sticky-notes/store', [NoteController::class, 'store'])->name('sticky-notes.store');
    Route::put('/sticky-notes/update/{id}', [NoteController::class, 'update'])->name('sticky-notes.update');
    Route::delete('/sticky-notes/destroy/{id}', [NoteController::class, 'destroy'])->name('sticky-notes.destroy');

    // Project Routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // user profile route
    Route::get('/user/profile', [UserCreateController::class, 'userProfile'])->name('user.profile');
    Route::post('/user/info', [UserCreateController::class, 'userinfo'])->name('user.info');


    // Chat Routes
    Route::get('/chat/{user}', [MessageController::class, 'index']);
    Route::post('/chat/send', [MessageController::class, 'store']);
    Route::get('/chat/unread-counts', [MessageController::class, 'unreadCounts']);

    // Notification Routes
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');


    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::post('/instagram/schedule', [MediaController::class, 'schedule'])
        ->name('instagram.schedule');
    Route::get('/scheduled-post/{id}', [MediaController::class, 'getPost']);
    Route::post('/scheduled-post/update/{id}', [MediaController::class, 'updatepost']);
    Route::delete('/scheduled-post/delete/{id}', [MediaController::class, 'deletePost'])->name('scheduled.delete');
});
/*
|--------------------------------------------------------------------------
| Role-Based Dashboards
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin'])->group(function () {

    // Dashboard Route
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'index'])->name('super-admin.dashboard');
    Route::get('/super-admin/departments/IT', [SuperAdminController::class, 'IT'])->name('departments.IT');
    Route::get('/super-admin/departments/social', [SuperAdminController::class, 'social'])->name('departments.social');

    // Employee Management Routes
    Route::get('/super-admin/create-users', [UserCreateController::class, 'index'])->name('users.index');
    Route::post('/super-admin/store-users', [UserCreateController::class, 'store'])->name('users.store');
    Route::delete('/super-admin/users/{id}', [UserCreateController::class, 'destroy'])->name('users.destroy');
    Route::put('/super-admin/users/{id}', [UserCreateController::class, 'update'])
        ->name('users.update');
    // Role Management Routes
    Route::resource('roles', RoleController::class);
    // Department Management Routes
    Route::resource('departments', DepartmentController::class);

    // Shift Management Routes
    Route::resource('shifts', ShiftController::class);

    // Attendance Routes
    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance-view', [AttendanceController::class, 'viewAttendance'])->name('attendance.viewattendance');
    Route::get('/super-admin/leave-requests', [LeaveRequestController::class, 'adminIndex'])->name('super-admin.leave.requests');
    Route::post('/super-admin/leave-requests/{id}/approve', [LeaveRequestController::class, 'approve'])->name('super-admin.leave.approve');
    Route::post('/super-admin/leave-requests/{id}/reject', [LeaveRequestController::class, 'reject'])->name('super-admin.leave.reject');

    // audit log route
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard Route
    Route::get('/admin/dashboard', [SuperAdminController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    // Dashboard Route
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');
});


/*
|--------------------------------------------------------------------------
| Google OAuth (Chat Integration)
|--------------------------------------------------------------------------
*/
Route::get('/googlechat', [GoogleChatController::class, 'index'])->name('googlechat.index');

// Google login flow
Route::get('auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// Google logout (only clears Google session, not Breeze user auth)
Route::post('auth/google/logout', [GoogleController::class, 'logout'])->name('google.logout');
