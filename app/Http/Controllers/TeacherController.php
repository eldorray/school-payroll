<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\TeacherAllowance;
use App\Imports\TeachersImport;
use App\Exports\TeachersTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function index()
    {
        $unitId = session('unit_id');
        $teachers = Teacher::where('unit_id', $unitId)->orderBy('name')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        return view('teachers.index', compact('teachers', 'activeYear'));
    }

    public function create()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        return view('teachers.create', compact('activeYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'nip' => 'nullable|string|unique:teachers,nip',
            'joined_at' => 'nullable|date',
            // Allowances array: ['allowance_name' => 'amount']
            'allowances' => 'nullable|array',
            'allowances.*.name' => 'required|string',
            'allowances.*.amount' => 'required|numeric|min:0',
            // Annual Settings
            'teaching_hours' => 'nullable|integer|min:0',
            'bpjs_amount' => 'nullable|numeric|min:0',
        ]);

        $teacher = Teacher::create([
            'unit_id' => session('unit_id'),
            'name' => $validated['name'],
            'position' => $validated['position'] ?? null,
            'nip' => $validated['nip'],
            'joined_at' => $validated['joined_at'],
        ]);

        // Process allowances and annual settings if active year exists
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            // Annual Hours
            if (isset($validated['teaching_hours'])) {
                $teacher->annualSettings()->create([
                    'academic_year_id' => $activeYear->id,
                    'teaching_hours_per_month' => $validated['teaching_hours'],
                    'bpjs_amount' => $validated['bpjs_amount'] ?? 0,
                ]);
            } elseif (isset($validated['bpjs_amount'])) {
                // If only BPJS is set (edge case but possible)
                $teacher->annualSettings()->create([
                    'academic_year_id' => $activeYear->id,
                    'teaching_hours_per_month' => 0,
                    'bpjs_amount' => $validated['bpjs_amount'],
                ]);
            }

            if (!empty($request->allowances)) {
                foreach ($request->allowances as $allowance) {
                    if ($allowance['amount'] > 0) {
                        TeacherAllowance::create([
                            'academic_year_id' => $activeYear->id,
                            'teacher_id' => $teacher->id,
                            'allowance_name' => $allowance['name'],
                            'amount' => $allowance['amount'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully.');
    }

    public function show(Teacher $teacher)
    {
        // Show profile + history
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $currentAllowances = [];
        
        if ($activeYear) {
            $currentAllowances = TeacherAllowance::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear->id)
                ->get();
        }

        return view('teachers.edit', compact('teacher', 'activeYear', 'currentAllowances'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'nip' => 'nullable|string|unique:teachers,nip,' . $teacher->id,
            'joined_at' => 'nullable|date',
            'allowances' => 'nullable|array',
            'allowances.*.name' => 'required|string',
            'allowances.*.amount' => 'required|numeric|min:0',
            'teaching_hours' => 'nullable|integer|min:0',
            'bpjs_amount' => 'nullable|numeric|min:0',
        ]);

        $teacher->update([
            'name' => $validated['name'],
            'position' => $validated['position'] ?? null,
            'nip' => $validated['nip'],
            'joined_at' => $validated['joined_at'],
        ]);

        // Update allowances for active year
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            // Update Annual Hours & BPJS
            if (isset($validated['teaching_hours']) || isset($validated['bpjs_amount'])) {
                $setting = $teacher->annualSettings()->where('academic_year_id', $activeYear->id)->first();
                $data = [];
                if (isset($validated['teaching_hours'])) $data['teaching_hours_per_month'] = $validated['teaching_hours'];
                if (isset($validated['bpjs_amount'])) $data['bpjs_amount'] = $validated['bpjs_amount'];
                
                $teacher->annualSettings()->updateOrCreate(
                    ['academic_year_id' => $activeYear->id],
                    $data
                );
            }

            // Simplest strategy: delete all allowances and recreate
            TeacherAllowance::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear->id)
                ->delete();

            if (!empty($request->allowances)) {
                foreach ($request->allowances as $allowance) {
                    if ($allowance['amount'] > 0) {
                        TeacherAllowance::create([
                            'academic_year_id' => $activeYear->id,
                            'teacher_id' => $teacher->id,
                            'allowance_name' => $allowance['name'],
                            'amount' => $allowance['amount'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete(); // Cascades will handle allowances/payrolls if setup, but I put constraints.
        // Actually migrations have cascadeOnDelete, so it's safe.
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted.');
    }

    /**
     * Toggle teacher active status for payroll
     */
    public function toggleActive(Teacher $teacher)
    {
        $teacher->update(['is_active' => !$teacher->is_active]);
        
        $status = $teacher->is_active ? 'diikutkan' : 'dikecualikan dari';
        return redirect()->route('teachers.index')
            ->with('success', "Guru {$teacher->name} berhasil {$status} proses penggajian.");
    }

    /**
     * Toggle teacher tahfidz status
     */
    public function toggleTahfidz(Teacher $teacher)
    {
        $teacher->update(['is_tahfidz' => !$teacher->is_tahfidz]);
        
        $status = $teacher->is_tahfidz ? 'dijadikan guru tahfidz' : 'diubah menjadi guru biasa';
        return redirect()->route('teachers.index')
            ->with('success', "Guru {$teacher->name} berhasil {$status}.");
    }

    /**
     * Show the import form
     */
    public function showImportForm()
    {
        return view('teachers.import');
    }

    /**
     * Import teachers from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls) atau CSV.',
            'file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        try {
            $unitId = session('unit_id');
            $import = new TeachersImport($unitId);
            
            Excel::import($import, $request->file('file'));

            $count = $import->getImportedCount();
            return redirect()->route('teachers.index')
                ->with('success', "Berhasil mengimpor {$count} data guru.");
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->back()
                ->withErrors(['file' => 'Terjadi kesalahan validasi: ' . implode('; ', $errorMessages)]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors with user-friendly messages
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'nip')) {
                return redirect()->back()
                    ->withErrors(['file' => 'Gagal import: Ada NIP yang sama dalam file Excel. Pastikan setiap guru memiliki NIP yang berbeda, atau kosongkan kolom NIP jika tidak ada.']);
            }
            return redirect()->back()
                ->withErrors(['file' => 'Terjadi kesalahan database. Silakan periksa kembali data Anda.']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['file' => 'Terjadi kesalahan saat mengimpor. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        return Excel::download(new TeachersTemplateExport, 'template_import_guru.xlsx');
    }
}

