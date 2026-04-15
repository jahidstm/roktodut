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
            ->with(['district:id,name', 'upazila:id,name'])
            ->withCount([
                'responses as accepted_responses_count' => fn($q) => $q->where('status', 'accepted'),
                'responses as claimed_verifications_count' => fn($q) => $q->where('verification_status', 'claimed'),
                'responses as verified_verifications_count' => fn($q) => $q->where('verification_status', 'verified'),
            ]);

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('district')) {
            $query->where('district_id', $request->district);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('hospital', 'like', "%{$search}%");
            });
        }

        $requests = $query
            ->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')")
            ->orderBy('needed_at', 'asc')
            ->paginate(12)
            ->withQueryString();

        $districts = \App\Models\District::orderBy('name', 'asc')->get();

        // Ajax/live-filter: return only the partial HTML
        if ($request->ajax()) {
            return view('public.requests.partials.list', compact('requests'));
        }

        return view('public.requests.index', compact('requests', 'districts'));
    }
}
