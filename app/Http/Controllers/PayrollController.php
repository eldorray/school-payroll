<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\Teacher;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $batches = collect();
        $selectedMonth = $request->get('month', date('n'));
        $selectedYear = $request->get('year', date('Y'));
        $unitId = session('unit_id');

        if ($activeYear) {
            $batches = PayrollBatch::with(['payrolls.teacher'])
                ->where('academic_year_id', $activeYear->id)
                ->where('unit_id', $unitId)
                ->where('month', $selectedMonth)
                ->where('year', $selectedYear)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('payrolls.index', compact('activeYear', 'batches', 'selectedMonth', 'selectedYear'));
    }

    public function create()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return redirect()->route('academic-years.index')->with('error', 'Please activate an Academic Year first.');
        }

        $unitId = session('unit_id');
        // Get only active NON-Tahfidz teachers for selected unit
        $teachers = Teacher::where('unit_id', $unitId)
            ->where('is_active', true)
            ->where('is_tahfidz', false)
            ->orderBy('name')
            ->get();
        
        $currentMonth = date('n');
        $currentYear = date('Y');

        return view('payrolls.create', compact('activeYear', 'teachers', 'currentMonth', 'currentYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'batch_name' => 'nullable|string|max:255',
            'attendance' => 'required|array',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return back()->with('error', 'No active academic year.');
        }

        $unitId = session('unit_id');
        $settings = $activeYear->getSettingsForUnit($unitId);
        if (!$settings) {
            return back()->with('error', 'Payroll settings not found for this year.');
        }

        $unitId = session('unit_id');

        DB::transaction(function () use ($validated, $activeYear, $settings, $unitId) {
            // Create a new batch for this payroll processing
            $batch = PayrollBatch::create([
                'academic_year_id' => $activeYear->id,
                'unit_id' => $unitId,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'name' => $validated['batch_name'] ?? null,
            ]);

            foreach ($validated['attendance'] as $teacherId => $data) {
                $days = $data['days'] ?? 0;
                
                // Fetch annual teaching hours
                $teacher = Teacher::find($teacherId);
                $annualSetting = $teacher->annualSettings->where('academic_year_id', $activeYear->id)->first();
                $hours = $annualSetting ? $annualSetting->teaching_hours_per_month : 0;
                $annualBpjs = $annualSetting ? $annualSetting->bpjs_amount : 0;

                // Deductions from input
                $deductionsInput = $data['deductions'] ?? [];
                $bpjs = $annualBpjs;
                $lateCount = $deductionsInput['late_count'] ?? 0;
                $lateRate = $settings->late_deduction_rate ?? 0;
                $lateDed = $lateCount * $lateRate;
                $incentiveDed = $deductionsInput['incentive_deduction'] ?? 0;
                $otherDed = $deductionsInput['other_deduction'] ?? 0;

                // 1. Calculate Salary Types
                $teachingSalary = $hours * $settings->teaching_rate_per_hour;
                $transportSalary = $days * $settings->transport_rate_per_visit;
                
                // 2. Tenure Calculation
                $tenureYears = 0;
                $tenureSalary = 0;
                if ($teacher && $teacher->joined_at) {
                    $tenureYears = (int) $teacher->joined_at->diffInYears(now());
                    $tenureSalary = round($tenureYears * $settings->masa_kerja_rate_per_year);
                }

                // 3. Allowances
                $allowances = $activeYear->teacherAllowances()->where('teacher_id', $teacherId)->get();
                $allowanceTotal = $allowances->sum('amount');

                // 4. Gross & Net
                $grossSalary = $teachingSalary + $transportSalary + $tenureSalary + $allowanceTotal;
                $totalDeductions = $bpjs + $lateDed + $incentiveDed + $otherDed;
                $netSalary = $grossSalary - $totalDeductions;

                // Snapshot details
                $details = [
                    'teaching_rate' => $settings->teaching_rate_per_hour,
                    'transport_rate' => $settings->transport_rate_per_visit,
                    'masa_kerja_rate' => $settings->masa_kerja_rate_per_year,
                    'late_deduction_rate' => $lateRate,
                    'tenure_years' => $tenureYears,
                    'allowances' => $allowances->pluck('amount', 'allowance_name')->toArray(),
                    'breakdown' => [
                        'teaching' => $teachingSalary,
                        'transport' => $transportSalary,
                        'tenure' => $tenureSalary,
                        'allowances' => $allowanceTotal,
                        'deductions' => [
                            'bpjs' => $bpjs,
                            'late_count' => $lateCount,
                            'late' => $lateDed,
                            'incentive' => $incentiveDed,
                            'other' => $otherDed
                        ]
                    ]
                ];

                // Create new payroll record (always create, never update)
                Payroll::create([
                    'payroll_batch_id' => $batch->id,
                    'academic_year_id' => $activeYear->id,
                    'unit_id' => $unitId,
                    'teacher_id' => $teacherId,
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                    'teaching_hours' => $hours,
                    'attendance_days' => $days,
                    'total_salary' => $netSalary,
                    'bpjs_amount' => $bpjs,
                    'transport_allowance_deduction_amount' => $lateDed,
                    'incentive_deduction_amount' => $incentiveDed,
                    'other_deduction_amount' => $otherDed,
                    'details' => $details,
                ]);
            }
        });

        return redirect()->route('payrolls.index', [
            'month' => $validated['month'],
            'year' => $validated['year']
        ])->with('success', 'Batch penggajian berhasil dibuat.');
    }

    public function show(Payroll $payroll)
    {
        return view('payrolls.show', compact('payroll'));
    }

    /**
     * Show batch details
     */
    public function showBatch(PayrollBatch $batch)
    {
        $batch->load(['payrolls.teacher', 'academicYear']);
        return view('payrolls.batch', compact('batch'));
    }

    /**
     * Edit batch form
     */
    public function editBatch(PayrollBatch $batch)
    {
        $batch->load(['payrolls.teacher', 'academicYear']);
        $activeYear = $batch->academicYear;
        $unitId = session('unit_id');
        $settings = $activeYear->getSettingsForUnit($unitId);
        
        return view('payrolls.edit-batch', compact('batch', 'activeYear', 'settings'));
    }

    /**
     * Update batch
     */
    public function updateBatch(Request $request, PayrollBatch $batch)
    {
        $validated = $request->validate([
            'batch_name' => 'nullable|string|max:255',
            'attendance' => 'required|array',
        ]);

        $activeYear = $batch->academicYear;
        $unitId = session('unit_id');
        $settings = $activeYear->getSettingsForUnit($unitId);

        DB::transaction(function () use ($validated, $batch, $activeYear, $settings) {
            // Update batch name
            $batch->update(['name' => $validated['batch_name'] ?? null]);

            // Update each payroll
            foreach ($validated['attendance'] as $payrollId => $data) {
                $payroll = Payroll::find($payrollId);
                if (!$payroll || $payroll->payroll_batch_id !== $batch->id) continue;

                $days = $data['days'] ?? 0;
                $teacher = $payroll->teacher;
                $annualSetting = $teacher->annualSettings->where('academic_year_id', $activeYear->id)->first();
                $hours = $annualSetting ? $annualSetting->teaching_hours_per_month : 0;
                $annualBpjs = $annualSetting ? $annualSetting->bpjs_amount : 0;

                $deductionsInput = $data['deductions'] ?? [];
                $bpjs = $annualBpjs;
                $lateCount = $deductionsInput['late_count'] ?? 0;
                $lateRate = $settings->late_deduction_rate ?? 0;
                $lateDed = $lateCount * $lateRate;
                $incentiveDed = $deductionsInput['incentive_deduction'] ?? 0;
                $otherDed = $deductionsInput['other_deduction'] ?? 0;

                $teachingSalary = $hours * $settings->teaching_rate_per_hour;
                $transportSalary = $days * $settings->transport_rate_per_visit;
                
                $tenureYears = 0;
                $tenureSalary = 0;
                if ($teacher && $teacher->joined_at) {
                    $tenureYears = (int) $teacher->joined_at->diffInYears(now());
                    $tenureSalary = round($tenureYears * $settings->masa_kerja_rate_per_year);
                }

                $allowances = $activeYear->teacherAllowances()->where('teacher_id', $teacher->id)->get();
                $allowanceTotal = $allowances->sum('amount');

                $grossSalary = $teachingSalary + $transportSalary + $tenureSalary + $allowanceTotal;
                $totalDeductions = $bpjs + $lateDed + $incentiveDed + $otherDed;
                $netSalary = $grossSalary - $totalDeductions;

                $details = [
                    'teaching_rate' => $settings->teaching_rate_per_hour,
                    'transport_rate' => $settings->transport_rate_per_visit,
                    'masa_kerja_rate' => $settings->masa_kerja_rate_per_year,
                    'late_deduction_rate' => $lateRate,
                    'tenure_years' => $tenureYears,
                    'allowances' => $allowances->pluck('amount', 'allowance_name')->toArray(),
                    'breakdown' => [
                        'teaching' => $teachingSalary,
                        'transport' => $transportSalary,
                        'tenure' => $tenureSalary,
                        'allowances' => $allowanceTotal,
                        'deductions' => [
                            'bpjs' => $bpjs,
                            'late_count' => $lateCount,
                            'late' => $lateDed,
                            'incentive' => $incentiveDed,
                            'other' => $otherDed
                        ]
                    ]
                ];

                $payroll->update([
                    'attendance_days' => $days,
                    'total_salary' => $netSalary,
                    'bpjs_amount' => $bpjs,
                    'transport_allowance_deduction_amount' => $lateDed,
                    'incentive_deduction_amount' => $incentiveDed,
                    'other_deduction_amount' => $otherDed,
                    'details' => $details,
                ]);
            }
        });

        return redirect()->route('payrolls.index', [
            'month' => $batch->month,
            'year' => $batch->year
        ])->with('success', 'Batch penggajian berhasil diupdate.');
    }

    public function report(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        $batchId = $request->get('batch');
        $activeYear = AcademicYear::where('is_active', true)->first();
        $unitId = session('unit_id');
        
        $query = Payroll::with('teacher')
            ->where('unit_id', $unitId)
            ->when($activeYear, function($q) use ($activeYear) {
                return $q->where('academic_year_id', $activeYear->id);
            });
        
        if ($batchId) {
            $query->where('payroll_batch_id', $batchId);
        } else {
            $query->where('month', $month)->where('year', $year);
        }
        
        $payrolls = $query->get();
        $batch = $batchId ? PayrollBatch::find($batchId) : null;
            
        return view('payrolls.report', compact('payrolls', 'month', 'year', 'activeYear', 'batch'));
    }

    public function printAll(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        $batchId = $request->get('batch');
        $activeYear = AcademicYear::where('is_active', true)->first();
        $unitId = session('unit_id');
        
        $query = Payroll::with('teacher', 'academicYear')
            ->where('unit_id', $unitId)
            ->when($activeYear, function($q) use ($activeYear) {
                return $q->where('academic_year_id', $activeYear->id);
            });
        
        if ($batchId) {
            $query->where('payroll_batch_id', $batchId);
        } else {
            $query->where('month', $month)->where('year', $year);
        }
        
        $payrolls = $query->get();
        $batch = $batchId ? PayrollBatch::find($batchId) : null;
            
        return view('payrolls.print_all', compact('payrolls', 'month', 'year', 'activeYear', 'batch'));
    }

    /**
     * Delete a specific batch
     */
    public function destroyBatch(PayrollBatch $batch)
    {
        $month = $batch->month;
        $year = $batch->year;
        $batchName = $batch->display_name;
        
        $batch->delete(); // Cascades will delete related payrolls
        
        return redirect()->route('payrolls.index', [
            'month' => $month,
            'year' => $year
        ])->with('success', "Batch \"{$batchName}\" berhasil dihapus.");
    }

    public function deleteMonth(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return back()->with('error', 'No active academic year.');
        }

        // Delete all batches for this month (cascades to payrolls)
        $deleted = PayrollBatch::where('academic_year_id', $activeYear->id)
            ->where('unit_id', session('unit_id'))
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->delete();

        $monthName = date('F', mktime(0, 0, 0, $validated['month'], 10));
        
        return redirect()->route('payrolls.index', [
            'month' => $validated['month'],
            'year' => $validated['year']
        ])->with('success', "Semua batch gaji untuk {$monthName} {$validated['year']} berhasil dihapus.");
    }
}
