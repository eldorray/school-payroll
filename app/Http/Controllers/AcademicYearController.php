<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'teaching_rate' => 'required|numeric|min:0',
            'transport_rate' => 'required|numeric|min:0',
            'masa_kerja_rate' => 'required|numeric|min:0',
            'late_deduction_rate' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $year = AcademicYear::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => false, // Default inactive
            ]);

            // Create default settings for this year
            PayrollSetting::create([
                'academic_year_id' => $year->id,
                'teaching_rate_per_hour' => $validated['teaching_rate'],
                'transport_rate_per_visit' => $validated['transport_rate'],
                'masa_kerja_rate_per_year' => $validated['masa_kerja_rate'],
                'late_deduction_rate' => $validated['late_deduction_rate'] ?? 0,
            ]);
        });

        return redirect()->route('academic-years.index')->with('success', 'Academic Year created successfully.');
    }

    public function show(AcademicYear $academicYear)
    {
        return view('academic-years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear)
    {
        $academicYear->load('payrollSettings');
        return view('academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'teaching_rate' => 'required|numeric|min:0',
            'transport_rate' => 'required|numeric|min:0',
            'masa_kerja_rate' => 'required|numeric|min:0',
            'late_deduction_rate' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $academicYear, $validated) {
            $academicYear->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            $academicYear->payrollSettings()->updateOrCreate(
                ['academic_year_id' => $academicYear->id],
                [
                    'teaching_rate_per_hour' => $validated['teaching_rate'],
                    'transport_rate_per_visit' => $validated['transport_rate'],
                    'masa_kerja_rate_per_year' => $validated['masa_kerja_rate'],
                    'late_deduction_rate' => $validated['late_deduction_rate'] ?? 0,
                ]
            );
        });

        return redirect()->route('academic-years.index')->with('success', 'Academic Year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->payrolls()->count() > 0) {
            return back()->with('error', 'Cannot delete Academic Year with existing payroll records.');
        }

        $academicYear->delete();
        return redirect()->route('academic-years.index')->with('success', 'Academic Year deleted successfully.');
    }

    public function activate(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            // Deactivate all others
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            
            // Activate target
            $academicYear->update(['is_active' => true]);
        });

        return redirect()->route('academic-years.index')->with('success', 'Academic Year activated.');
    }
}
