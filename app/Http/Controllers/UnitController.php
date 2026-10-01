<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function switchUnit(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|integer|exists:units,id',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|between:2000,2100',
        ]);

        $request->session()->put('unit_id', $request->integer('unit_id'));

        return redirect()->route('dashboard', $request->only(['month', 'year']));
    }

    public function edit()
    {
        $unitId = session('unit_id');
        $unit = Unit::findOrFail($unitId);
        
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request)
    {
        $unitId = session('unit_id');
        $unit = Unit::findOrFail($unitId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'principal_name' => 'required|string|max:255',
            'signature_date' => 'required|date',
        ]);

        $unit->update($validated);

        return redirect()->route('units.edit')->with('success', 'Unit settings updated successfully.');
    }
}
