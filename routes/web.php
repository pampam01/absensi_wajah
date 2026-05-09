<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FaceRegistrationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;

Route::get('/', function () {
    return redirect()->route('login'); // Redirect to login as default
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('employees', EmployeeController::class);
    Route::patch('employees/{employee}/toggle-active', [EmployeeController::class, 'toggleActive'])->name('employees.toggle-active');

    Route::get('face-registration/create', [FaceRegistrationController::class, 'create'])->name('face-registration.create');
    Route::post('face-registration', [FaceRegistrationController::class, 'store'])->name('face-registration.store');

    Route::get('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::get('attendance/employees', [AttendanceController::class, 'employees'])->name('attendance.employees');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('attendance-report', [AttendanceReportController::class, 'index'])->name('attendance-report.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
