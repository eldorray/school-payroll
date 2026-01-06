<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TahfidzPayrollController;
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
    Route::post('teachers/{teacher}/toggle-tahfidz', [TeacherController::class, 'toggleTahfidz'])->name('teachers.toggle-tahfidz');
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

    // Extracurriculars (Master Data)
    Route::resource('extracurriculars', \App\Http\Controllers\ExtracurricularController::class);

    // Extracurricular Payrolls
    Route::get('extracurricular-payrolls/report', [\App\Http\Controllers\ExtracurricularPayrollController::class, 'report'])->name('extracurricular-payrolls.report');
    Route::get('extracurricular-payrolls/print-all', [\App\Http\Controllers\ExtracurricularPayrollController::class, 'printAll'])->name('extracurricular-payrolls.print_all');
    Route::get('extracurricular-payrolls', [\App\Http\Controllers\ExtracurricularPayrollController::class, 'index'])->name('extracurricular-payrolls.index');
    Route::get('extracurricular-payrolls/create', [\App\Http\Controllers\ExtracurricularPayrollController::class, 'create'])->name('extracurricular-payrolls.create');
    Route::post('extracurricular-payrolls', [\App\Http\Controllers\ExtracurricularPayrollController::class, 'store'])->name('extracurricular-payrolls.store');
    Route::get('extracurricular-payrolls/{extracurricularPayroll}', [\App\Http\Controllers\ExtracurricularPayrollController::class, 'show'])->name('extracurricular-payrolls.show');
    Route::delete('extracurricular-payrolls/{extracurricularPayroll}', [\App\Http\Controllers\ExtracurricularPayrollController::class, 'destroy'])->name('extracurricular-payrolls.destroy');

    // Tahfidz Payrolls (hanya untuk MI Daarul Hikmah)
    Route::get('tahfidz-payrolls/report', [TahfidzPayrollController::class, 'report'])->name('tahfidz-payrolls.report');
    Route::get('tahfidz-payrolls/print-all', [TahfidzPayrollController::class, 'printAll'])->name('tahfidz-payrolls.print_all');
    Route::get('tahfidz-payrolls', [TahfidzPayrollController::class, 'index'])->name('tahfidz-payrolls.index');
    Route::get('tahfidz-payrolls/create', [TahfidzPayrollController::class, 'create'])->name('tahfidz-payrolls.create');
    Route::post('tahfidz-payrolls', [TahfidzPayrollController::class, 'store'])->name('tahfidz-payrolls.store');
    Route::get('tahfidz-payrolls/{payroll}', [TahfidzPayrollController::class, 'show'])->name('tahfidz-payrolls.show');
    Route::delete('tahfidz-payrolls/batch/{batch}', [TahfidzPayrollController::class, 'destroyBatch'])->name('tahfidz-payrolls.batch.destroy');

    // Backup & Restore
    Route::get('backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
    Route::post('backups/backup', [\App\Http\Controllers\BackupController::class, 'backup'])->name('backups.backup');
    Route::get('backups/download/{filename}', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
    Route::post('backups/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('backups/{filename}', [\App\Http\Controllers\BackupController::class, 'delete'])->name('backups.delete');
});

require __DIR__.'/auth.php';
