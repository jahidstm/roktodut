<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ambulance;
use App\Models\Division;

class AmbulanceSubmissionController extends Controller
{
    public function create()
    {
        $divisions = Division::all();
        return view('ambulances.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'type' => 'required|in:non-ac,ac,icu,nicu,freezer',
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'upazila_id' => 'required|exists:upazilas,id',
            'vehicle_number' => 'nullable|string|max:255',
        ]);

        $validated['added_by'] = auth()->id();
        $validated['is_verified'] = false; // Pending status
        $validated['status'] = 'active';

        Ambulance::create($validated);

        return redirect()->route('ambulances.index')
            ->with('success', 'ধন্যবাদ! আপনার সাবমিট করা অ্যাম্বুলেন্সটি রিভিউয়ের জন্য অ্যাডমিন প্যানেলে পাঠানো হয়েছে। ভেরিফাই হওয়ার পর এটি ডিরেক্টরিতে যুক্ত হবে এবং আপনি 5 পয়েন্ট পাবেন।');
    }
}
