<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// Redirect root to login or dashboard
Route::get('/', function () {
    return redirect()->route('login');
});

// Protected routes (require authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Academic Years
    Route::resource('academic-years', AcademicYearController::class);
    Route::post('academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');

    // Teachers
    Route::get('teachers/import', [TeacherController::class, 'showImportForm'])->name('teachers.import');
    Route::get('teachers/import/template', [TeacherController::class, 'downloadTemplate'])->name('teachers.import.template');
    Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import.store');
    Route::post('teachers/{teacher}/toggle-active', [TeacherController::class, 'toggleActive'])->name('teachers.toggle-active');
    Route::resource('teachers', TeacherController::class);

    // Payrolls - Custom routes BEFORE resource to avoid conflicts
    Route::get('payrolls/report', [PayrollController::class, 'report'])->name('payrolls.report');
    Route::get('payrolls/print-all', [PayrollController::class, 'printAll'])->name('payrolls.print_all');
    Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('payrolls/create', [PayrollController::class, 'create'])->name('payrolls.create');
    Route::post('payrolls', [PayrollController::class, 'store'])->name('payrolls.store');
    Route::get('payrolls/batch/{batch}', [PayrollController::class, 'showBatch'])->name('payrolls.batch');
    Route::get('payrolls/batch/{batch}/edit', [PayrollController::class, 'editBatch'])->name('payrolls.batch.edit');
    Route::patch('payrolls/batch/{batch}', [PayrollController::class, 'updateBatch'])->name('payrolls.batch.update');
    Route::delete('payrolls/batch/{batch}', [PayrollController::class, 'destroyBatch'])->name('payrolls.batch.destroy');
    Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::delete('payrolls/delete-month', [PayrollController::class, 'deleteMonth'])->name('payrolls.delete_month');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Unit Settings
    Route::get('/unit-settings', [\App\Http\Controllers\UnitController::class, 'edit'])->name('units.edit');
    Route::patch('/unit-settings', [\App\Http\Controllers\UnitController::class, 'update'])->name('units.update');
});

require __DIR__.'/auth.php';
