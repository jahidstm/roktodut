<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Illuminate\Http\Request;

class PublicBloodRequestController extends Controller
{
    /**
     * Display a listing of public blood requests.
     */
    public function index(Request $request)
    {
        $query = BloodRequest::active()
            ->with(['district:id,name', 'upazila:id,name']);

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('district')) {
            $query->where('district_id', $request->district);
        }

        $requests = $query->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')")
            ->orderBy('needed_at', 'asc')
            ->paginate(12)
            ->withQueryString();

        $districts = \App\Models\District::orderBy('name', 'asc')->get();

        return view('public.requests.index', compact('requests', 'districts'));
    }
}
