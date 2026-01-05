<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\ExtracurricularPayroll;
use App\Models\Teacher;
use App\Models\Unit;
use Illuminate\Http\Request;

class ExtracurricularPayrollController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('unit_id');
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedMonth = $request->get('month', date('n'));
        $selectedYear = $request->get('year', date('Y'));

        $payrolls = collect();
        if ($activeYear) {
            $payrolls = ExtracurricularPayroll::with(['teacher', 'extracurricular'])
                ->where('unit_id', $unitId)
                ->where('academic_year_id', $activeYear->id)
                ->where('month', $selectedMonth)
                ->where('year', $selectedYear)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('extracurricular-payrolls.index', compact(
            'payrolls', 'activeYear', 'selectedMonth', 'selectedYear'
        ));
    }

    public function create()
    {
        $unitId = session('unit_id');
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            return redirect()->route('academic-years.index')
                ->with('error', 'Silakan aktifkan Tahun Ajaran terlebih dahulu.');
        }

        $teachers = Teacher::where('unit_id', $unitId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $extracurriculars = Extracurricular::where('unit_id', $unitId)
            ->orderBy('name')
            ->get();

        $currentMonth = date('n');
        $currentYear = date('Y');

        return view('extracurricular-payrolls.create', compact(
            'teachers', 'extracurriculars', 'activeYear', 'currentMonth', 'currentYear'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'entries' => 'required|array|min:1',
            'entries.*.teacher_id' => 'required|exists:teachers,id',
            'entries.*.extracurricular_id' => 'required|exists:extracurriculars,id',
            'entries.*.volume' => 'required|integer|min:0',
        ]);

        $unitId = session('unit_id');
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        foreach ($validated['entries'] as $entry) {
            if ($entry['volume'] <= 0) continue;

            $extracurricular = Extracurricular::find($entry['extracurricular_id']);
            $rate = $extracurricular->rate;
            $total = $entry['volume'] * $rate;

            ExtracurricularPayroll::create([
                'unit_id' => $unitId,
                'academic_year_id' => $activeYear->id,
                'teacher_id' => $entry['teacher_id'],
                'extracurricular_id' => $entry['extracurricular_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
                'volume' => $entry['volume'],
                'rate' => $rate,
                'total' => $total,
            ]);
        }

        return redirect()->route('extracurricular-payrolls.index', [
            'month' => $validated['month'],
            'year' => $validated['year']
        ])->with('success', 'Data penggajian ekskul berhasil disimpan.');
    }

    public function destroy(ExtracurricularPayroll $extracurricularPayroll)
    {
        $month = $extracurricularPayroll->month;
        $year = $extracurricularPayroll->year;
        
        $extracurricularPayroll->delete();

        return redirect()->route('extracurricular-payrolls.index', [
            'month' => $month,
            'year' => $year
        ])->with('success', 'Data penggajian ekskul berhasil dihapus.');
    }

    public function show(ExtracurricularPayroll $extracurricularPayroll)
    {
        $extracurricularPayroll->load(['teacher', 'extracurricular', 'academicYear']);
        $unit = Unit::find(session('unit_id'));
        $activeYear = $extracurricularPayroll->academicYear;
        
        return view('extracurricular-payrolls.show', compact('extracurricularPayroll', 'unit', 'activeYear'));
    }

    public function report(Request $request)
    {
        $unitId = session('unit_id');
        $unit = Unit::find($unitId);
        $activeYear = AcademicYear::where('is_active', true)->first();
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $payrolls = ExtracurricularPayroll::with(['teacher', 'extracurricular'])
            ->where('unit_id', $unitId)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('teacher_id')
            ->get();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $months[$month] ?? '';

        return view('extracurricular-payrolls.report', compact(
            'payrolls', 'unit', 'activeYear', 'month', 'year', 'monthName'
        ));
    }

    public function printAll(Request $request)
    {
        $unitId = session('unit_id');
        $unit = Unit::find($unitId);
        $activeYear = AcademicYear::where('is_active', true)->first();
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $payrolls = ExtracurricularPayroll::with(['teacher', 'extracurricular'])
            ->where('unit_id', $unitId)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('teacher_id')
            ->get();

        return view('extracurricular-payrolls.print_all', compact(
            'payrolls', 'unit', 'activeYear', 'month', 'year'
        ));
    }
}
