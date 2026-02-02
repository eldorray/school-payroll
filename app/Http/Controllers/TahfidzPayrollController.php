<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\Teacher;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahfidzPayrollController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('unit_id');
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedMonth = $request->get('month', date('n'));
        $selectedYear = $request->get('year', date('Y'));

        $batches = collect();
        if ($activeYear) {
            $batches = PayrollBatch::with(['payrolls.teacher'])
                ->where('unit_id', $unitId)
                ->where('academic_year_id', $activeYear->id)
                ->where('month', $selectedMonth)
                ->where('year', $selectedYear)
                ->where('is_tahfidz', true)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('tahfidz-payrolls.index', compact(
            'batches', 'activeYear', 'selectedMonth', 'selectedYear'
        ));
    }

    public function create()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return redirect()->route('academic-years.index')
                ->with('error', 'Silakan aktifkan Tahun Ajaran terlebih dahulu.');
        }

        $unitId = session('unit_id');
        // Get only active TAHFIDZ teachers
        $teachers = Teacher::where('unit_id', $unitId)
            ->where('is_active', true)
            ->where('is_tahfidz', true)
            ->orderBy('name')
            ->get();

        $currentMonth = date('n');
        $currentYear = date('Y');

        return view('tahfidz-payrolls.create', compact('activeYear', 'teachers', 'currentMonth', 'currentYear'));
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
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $unitId = session('unit_id');
        $settings = $activeYear->getSettingsForUnit($unitId);
        if (!$settings) {
            return back()->with('error', 'Pengaturan tarif belum diatur untuk tahun ini.');
        }

        $unitId = session('unit_id');

        DB::transaction(function () use ($validated, $activeYear, $settings, $unitId) {
            // Create a new batch for tahfidz payroll
            $batch = PayrollBatch::create([
                'academic_year_id' => $activeYear->id,
                'unit_id' => $unitId,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'name' => $validated['batch_name'] ?? null,
                'is_tahfidz' => true,
            ]);

            foreach ($validated['attendance'] as $teacherId => $data) {
                $days = $data['days'] ?? 0;
                
                $teacher = Teacher::find($teacherId);
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

                $allowances = $activeYear->teacherAllowances()->where('teacher_id', $teacherId)->get();
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
                    'is_tahfidz' => true,
                ]);
            }
        });

        return redirect()->route('tahfidz-payrolls.index', [
            'month' => $validated['month'],
            'year' => $validated['year']
        ])->with('success', 'Batch gaji tahfidz berhasil dibuat.');
    }

    public function destroyBatch(PayrollBatch $batch)
    {
        $month = $batch->month;
        $year = $batch->year;
        
        // Delete all payrolls in this batch first
        $batch->payrolls()->delete();
        $batch->delete();

        return redirect()->route('tahfidz-payrolls.index', [
            'month' => $month,
            'year' => $year
        ])->with('success', 'Batch gaji tahfidz berhasil dihapus.');
    }

    public function show(Payroll $payroll)
    {
        return view('tahfidz-payrolls.show', compact('payroll'));
    }

    public function printAll(Request $request)
    {
        $unitId = session('unit_id');
        $activeYear = AcademicYear::where('is_active', true)->first();
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $payrolls = Payroll::with(['teacher', 'academicYear'])
            ->where('unit_id', $unitId)
            ->where('is_tahfidz', true)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('id')
            ->get();

        return view('tahfidz-payrolls.print_all', compact('payrolls', 'month', 'year'));
    }

    public function report(Request $request)
    {
        $unitId = session('unit_id');
        $unit = Unit::find($unitId);
        $activeYear = AcademicYear::where('is_active', true)->first();
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $payrolls = Payroll::with('teacher')
            ->where('unit_id', $unitId)
            ->where('is_tahfidz', true)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('id')
            ->get();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $months[$month] ?? '';

        return view('tahfidz-payrolls.report', compact(
            'payrolls', 'unit', 'activeYear', 'month', 'year', 'monthName'
        ));
    }
}
