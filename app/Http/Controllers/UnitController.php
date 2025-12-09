<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
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
