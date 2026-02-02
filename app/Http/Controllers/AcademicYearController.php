<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\PayrollSetting;
use App\Models\Unit;
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
        $units = Unit::all();
        return view('academic-years.create', compact('units'));
    }

    public function store(Request $request)
    {
        $units = Unit::all();
        
        // Build validation rules for each unit
        $rules = [
            'name' => 'required|string|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
        
        foreach ($units as $unit) {
            $rules["units.{$unit->id}.teaching_rate"] = 'required|numeric|min:0';
            $rules["units.{$unit->id}.transport_rate"] = 'required|numeric|min:0';
            $rules["units.{$unit->id}.masa_kerja_rate"] = 'required|numeric|min:0';
            $rules["units.{$unit->id}.late_deduction_rate"] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($request, $validated, $units) {
            $year = AcademicYear::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => false,
            ]);

            // Create settings for each unit
            foreach ($units as $unit) {
                $unitData = $request->input("units.{$unit->id}");
                PayrollSetting::create([
                    'academic_year_id' => $year->id,
                    'unit_id' => $unit->id,
                    'teaching_rate_per_hour' => $unitData['teaching_rate'],
                    'transport_rate_per_visit' => $unitData['transport_rate'],
                    'masa_kerja_rate_per_year' => $unitData['masa_kerja_rate'],
                    'late_deduction_rate' => $unitData['late_deduction_rate'] ?? 0,
                ]);
            }
        });

        return redirect()->route('academic-years.index')->with('success', 'Tahun Ajaran berhasil dibuat.');
    }

    public function show(AcademicYear $academicYear)
    {
        return view('academic-years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear)
    {
        $units = Unit::all();
        $academicYear->load('payrollSettings');
        
        // Create a map of unit_id => settings for easy access in view
        $settingsMap = $academicYear->payrollSettings->keyBy('unit_id');
        
        return view('academic-years.edit', compact('academicYear', 'units', 'settingsMap'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $units = Unit::all();
        
        // Build validation rules
        $rules = [
            'name' => 'required|string|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
        
        foreach ($units as $unit) {
            $rules["units.{$unit->id}.teaching_rate"] = 'required|numeric|min:0';
            $rules["units.{$unit->id}.transport_rate"] = 'required|numeric|min:0';
            $rules["units.{$unit->id}.masa_kerja_rate"] = 'required|numeric|min:0';
            $rules["units.{$unit->id}.late_deduction_rate"] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($request, $academicYear, $validated, $units) {
            $academicYear->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            // Update or create settings for each unit
            foreach ($units as $unit) {
                $unitData = $request->input("units.{$unit->id}");
                PayrollSetting::updateOrCreate(
                    [
                        'academic_year_id' => $academicYear->id,
                        'unit_id' => $unit->id,
                    ],
                    [
                        'teaching_rate_per_hour' => $unitData['teaching_rate'],
                        'transport_rate_per_visit' => $unitData['transport_rate'],
                        'masa_kerja_rate_per_year' => $unitData['masa_kerja_rate'],
                        'late_deduction_rate' => $unitData['late_deduction_rate'] ?? 0,
                    ]
                );
            }
        });

        return redirect()->route('academic-years.index')->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->payrolls()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus Tahun Ajaran yang memiliki data penggajian.');
        }

        $academicYear->delete();
        return redirect()->route('academic-years.index')->with('success', 'Tahun Ajaran berhasil dihapus.');
    }

    public function activate(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            // Deactivate all others
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            
            // Activate target
            $academicYear->update(['is_active' => true]);
        });

        return redirect()->route('academic-years.index')->with('success', 'Tahun Ajaran berhasil diaktifkan.');
    }
}
