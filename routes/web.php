<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PayrollController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('academic-years', AcademicYearController::class);
Route::post('academic-years/{academic_year}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');

Route::resource('teachers', TeacherController::class);

Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
Route::get('payrolls/create', [PayrollController::class, 'create'])->name('payrolls.create');
Route::post('payrolls', [PayrollController::class, 'store'])->name('payrolls.store');
Route::get('payrolls/report', [PayrollController::class, 'report'])->name('payrolls.report');
Route::get('payrolls/print-all', [PayrollController::class, 'printAll'])->name('payrolls.print_all');
Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
