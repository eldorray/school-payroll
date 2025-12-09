<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Payroll;
use App\Models\Teacher;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $payrolls = [];
        $selectedMonth = $request->get('month', date('n'));
        $selectedYear = $request->get('year', date('Y'));

        if ($activeYear) {
            $payrolls = Payroll::with('teacher')
                ->where('academic_year_id', $activeYear->id)
                ->where('month', $selectedMonth)
                ->where('year', $selectedYear)
                ->get();
        }

        return view('payrolls.index', compact('activeYear', 'payrolls', 'selectedMonth', 'selectedYear'));
    }

    public function create()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return redirect()->route('academic-years.index')->with('error', 'Please activate an Academic Year first.');
        }

        // Get all teachers
        $teachers = Teacher::orderBy('name')->get();
        
        $currentMonth = date('n');
        $currentYear = date('Y');

        return view('payrolls.create', compact('activeYear', 'teachers', 'currentMonth', 'currentYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'attendance' => 'required|array', // ['teacher_id' => ['hours' => x, 'days' => y, 'deductions' => [...]]]
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return back()->with('error', 'No active academic year.');
        }

        $settings = $activeYear->payrollSettings;
        if (!$settings) {
            return back()->with('error', 'Payroll settings not found for this year.');
        }

        DB::transaction(function () use ($validated, $activeYear, $settings) {
            foreach ($validated['attendance'] as $teacherId => $data) {
                $days = $data['days'] ?? 0;
                
                // Fetch annual teaching hours
                $teacher = Teacher::find($teacherId);
                $annualSetting = $teacher->annualSettings->where('academic_year_id', $activeYear->id)->first();
                $hours = $annualSetting ? $annualSetting->teaching_hours_per_month : 0;
                $annualBpjs = $annualSetting ? $annualSetting->bpjs_amount : 0;
                // Alternatively, could allow override from input if needed, but requirement says "input only once a year".
                // We will ignore input 'hours' and use setting.

                
                // Deductions from input
                $deductionsInput = $data['deductions'] ?? [];
                // Default to 0 if not present
                // $bpjs = $deductionsInput['bpjs'] ?? 0; // Use annual setting now
                $bpjs = $annualBpjs;
                $transportDed = $deductionsInput['transport_deduction'] ?? 0; // "Terlambat"
                $incentiveDed = $deductionsInput['incentive_deduction'] ?? 0; // "Insentif"
                $otherDed = $deductionsInput['other_deduction'] ?? 0; // "Jml" / others

                // 1. Calculate Salary Types
                $teachingSalary = $hours * $settings->teaching_rate_per_hour;
                $transportSalary = $days * $settings->transport_rate_per_visit;
                
                
                // 2. Tenure Calculation (Already fetched teacher)
                $tenureYears = 0;
                // 2. Tenure Calculation (Already fetched teacher)
                $tenureYears = 0;
                $tenureSalary = 0;
                if ($teacher && $teacher->joined_at) {
                    $tenureYears = (int) $teacher->joined_at->diffInYears(now()); // Ensure integer years (floor)
                    $tenureSalary = round($tenureYears * $settings->masa_kerja_rate_per_year); // Calculate and round amount
                }

                // 3. Allowances
                $allowances = $activeYear->teacherAllowances()->where('teacher_id', $teacherId)->get();
                $allowanceTotal = $allowances->sum('amount');

                // 4. Gross & Net
                $grossSalary = $teachingSalary + $transportSalary + $tenureSalary + $allowanceTotal;
                $totalDeductions = $bpjs + $transportDed + $incentiveDed + $otherDed;
                $netSalary = $grossSalary - $totalDeductions;

                // Snapshot details
                $details = [
                    'teaching_rate' => $settings->teaching_rate_per_hour,
                    'transport_rate' => $settings->transport_rate_per_visit,
                    'masa_kerja_rate' => $settings->masa_kerja_rate_per_year,
                    'tenure_years' => $tenureYears,
                    'allowances' => $allowances->pluck('amount', 'allowance_name')->toArray(),
                    'breakdown' => [
                        'teaching' => $teachingSalary,
                        'transport' => $transportSalary,
                        'tenure' => $tenureSalary,
                        'allowances' => $allowanceTotal,
                        'deductions' => [
                            'bpjs' => $bpjs,
                            'transport' => $transportDed,
                            'incentive' => $incentiveDed,
                            'other' => $otherDed
                        ]
                    ]
                ];

                Payroll::updateOrCreate(
                    [
                        'academic_year_id' => $activeYear->id,
                        'teacher_id' => $teacherId,
                        'month' => $validated['month'],
                        'year' => $validated['year'],
                    ],
                    [
                        'teaching_hours' => $hours,
                        'attendance_days' => $days,
                        'total_salary' => $netSalary,
                        'bpjs_amount' => $bpjs,
                        'transport_allowance_deduction_amount' => $transportDed,
                        'incentive_deduction_amount' => $incentiveDed,
                        'other_deduction_amount' => $otherDed,
                        'details' => $details,
                    ]
                );
            }
        });

        return redirect()->route('payrolls.index')->with('success', 'Payroll processed successfully.');
    }

    public function show(Payroll $payroll)
    {
        // Slip view
        return view('payrolls.show', compact('payroll'));
    }

    public function report(Request $request)
    {
        // Monthly report view (printable)
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        $payrolls = Payroll::with('teacher')
            ->where('month', $month)
            ->where('year', $year)
            ->when($activeYear, function($q) use ($activeYear) {
                 return $q->where('academic_year_id', $activeYear->id);
            })
            ->get();
            
        return view('payrolls.report', compact('payrolls', 'month', 'year', 'activeYear'));
    }

    public function printAll(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        $payrolls = Payroll::with('teacher', 'academicYear')
            ->where('month', $month)
            ->where('year', $year)
            ->when($activeYear, function($q) use ($activeYear) {
                 return $q->where('academic_year_id', $activeYear->id);
            })
            ->get();
            
        return view('payrolls.print_all', compact('payrolls', 'month', 'year', 'activeYear'));
    }
}
