<?php

namespace App\Http\Controllers;

use App\Enums\BloodComponentType;
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

        if ($request->filled('component_type')) {
            $query->where('component_type', $request->component_type);
        }

        if ($request->boolean('dengue_mode')) {
            $query->where('component_type', BloodComponentType::PLATELETS->value);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('hospital', 'like', "%{$search}%");
            });
        }

        $requests = $query
            ->orderByRaw("CASE urgency WHEN 'emergency' THEN 1 WHEN 'urgent' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
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
