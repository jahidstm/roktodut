<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ambulance;
use App\Models\Division;
use Illuminate\Support\Facades\Auth;

class AmbulanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->organization_id) {
            abort(403, 'Unauthorized access.');
        }

        $ambulances = Ambulance::with(['division', 'district', 'upazila'])
            ->where('organization_id', $user->organization_id)
            ->latest()
            ->paginate(15);

        return view('org.ambulances.index', compact('ambulances'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('org.ambulances.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->organization_id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'type' => 'required|in:non-ac,ac,icu,nicu,freezer',
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'upazila_id' => 'required|exists:upazilas,id',
            'vehicle_number' => 'nullable|string|max:255',
        ]);

        $validated['added_by'] = $user->id;
        $validated['organization_id'] = $user->organization_id;
        $validated['is_verified'] = true; // Auto-verified for organizations
        $validated['status'] = 'active';

        Ambulance::create($validated);

        return redirect()->route('org.ambulances.index')
            ->with('success', 'নতুন অ্যাম্বুলেন্স সফলভাবে যুক্ত করা হয়েছে এবং এটি অটো-ভেরিফাইড অবস্থায় ডিরেক্টরিতে প্রকাশিত হয়েছে।');
    }

    public function destroy(Ambulance $ambulance)
    {
        $user = Auth::user();
        if ($ambulance->organization_id !== $user->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        $ambulance->delete();
        return back()->with('success', 'অ্যাম্বুলেন্স ডিলিট করা হয়েছে।');
    }
}
