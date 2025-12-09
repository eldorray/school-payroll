<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class DashboardController extends Controller
{
    public function index()
    {
        $unitId = session('unit_id');
        $unit = Unit::find($unitId);
        
        return view('dashboard', compact('unit'));
    }
}
