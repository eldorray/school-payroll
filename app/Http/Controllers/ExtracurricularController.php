<?php

namespace App\Http\Controllers;

use App\Models\Extracurricular;
use Illuminate\Http\Request;

class ExtracurricularController extends Controller
{
    public function index()
    {
        $unitId = session('unit_id');
        $extracurriculars = Extracurricular::where('unit_id', $unitId)
            ->orderBy('name')
            ->get();
        
        return view('extracurriculars.index', compact('extracurriculars'));
    }

    public function create()
    {
        return view('extracurriculars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
        ]);

        Extracurricular::create([
            'unit_id' => session('unit_id'),
            'name' => $validated['name'],
            'rate' => $validated['rate'],
        ]);

        return redirect()->route('extracurriculars.index')
            ->with('success', 'Data ekskul berhasil ditambahkan.');
    }

    public function edit(Extracurricular $extracurricular)
    {
        return view('extracurriculars.edit', compact('extracurricular'));
    }

    public function update(Request $request, Extracurricular $extracurricular)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
        ]);

        $extracurricular->update($validated);

        return redirect()->route('extracurriculars.index')
            ->with('success', 'Data ekskul berhasil diupdate.');
    }

    public function destroy(Extracurricular $extracurricular)
    {
        $extracurricular->delete();
        
        return redirect()->route('extracurriculars.index')
            ->with('success', 'Data ekskul berhasil dihapus.');
    }
}
