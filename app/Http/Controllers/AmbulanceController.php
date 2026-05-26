<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Ambulance::with(['division', 'district', 'upazila'])
            ->where('status', 'active')
            ->where('is_verified', true);

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }
        if ($request->filled('upazila_id')) {
            $query->where('upazila_id', $request->upazila_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sort by ID desc (newest first)
        $ambulances = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
        
        $divisions = \App\Models\Division::all();

        return view('ambulances.index', compact('ambulances', 'divisions'));
    }
}
