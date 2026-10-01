<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ExtracurricularPayroll;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\Teacher;
use App\Models\Unit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['month' => 'nullable|integer|between:1,12', 'year' => 'nullable|integer|between:2000,2100']);
        $period = now()->subMonthNoOverflow()->startOfMonth()->setDate(
            $request->integer('year', now()->subMonthNoOverflow()->year),
            $request->integer('month', now()->subMonthNoOverflow()->month), 1
        );
        $unitId = session('unit_id');
        $unit = Unit::find($unitId);
        $units = Unit::orderBy('name')->get(['id', 'name']);
        $activeYear = AcademicYear::where('is_active', true)->first();
        $teacherCount = Teacher::where('unit_id', $unitId)->where('is_active', true)->count();
        $payrolls = Payroll::where('unit_id', $unitId)->where('month', $period->month)->where('year', $period->year)->get();
        $extras = ExtracurricularPayroll::where('unit_id', $unitId)->where('month', $period->month)->where('year', $period->year)->get();
        $totalPaid = $payrolls->sum('total_salary') + $extras->sum('total');
        $recipientCount = $payrolls->pluck('teacher_id')->merge($extras->pluck('teacher_id'))->unique()->count();
        $deductions = $payrolls->sum(fn ($payroll) => array_sum(data_get($payroll->details, 'breakdown.deductions', [])));
        $composition = collect([
            'Mengajar' => $payrolls->sum(fn ($payroll) => data_get($payroll->details, 'breakdown.teaching', 0)),
            'Transport' => $payrolls->sum(fn ($payroll) => data_get($payroll->details, 'breakdown.transport', 0)),
            'Tunjangan & masa kerja' => $payrolls->sum(fn ($payroll) => data_get($payroll->details, 'breakdown.allowances', 0) + data_get($payroll->details, 'breakdown.tenure', 0)),
            'Ekskul' => $extras->sum('total'),
        ]);
        $batches = PayrollBatch::where('unit_id', $unitId)->withCount('payrolls')->withSum('payrolls', 'total_salary')->latest()->limit(8)->get();

        return view('dashboard', compact('units', 'unit', 'activeYear', 'teacherCount', 'period', 'payrolls', 'extras', 'totalPaid', 'recipientCount', 'deductions', 'composition', 'batches'));
    }
}
