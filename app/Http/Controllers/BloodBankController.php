<?php

namespace App\Http\Controllers;

use App\Enums\BloodGroup;
use App\Models\BloodInventory;
use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use App\Models\Organization;
use Illuminate\Http\Request;

class BloodBankController extends Controller
{
    /**
     * Blood Bank Search Landing Page
     * GET /blood-bank
     */
    public function index(Request $request)
    {
        $divisions = Division::orderBy('name')->get();
        $districts = collect();
        $upazilas = collect();

        if ($request->filled('division_id')) {
            $districts = District::where('division_id', $request->division_id)
                ->orderBy('name')->get();
        }

        if ($request->filled('district_id')) {
            $upazilas = Upazila::where('district_id', $request->district_id)
                ->orderBy('name')->get();
        }

        // Default: সব blood bank দেখাই, filter না থাকলে
        $query = Organization::query()
            ->where('is_blood_bank', true)
            ->where('status', 'verified')
            ->with(['bloodInventories']);

        if ($request->filled('district_id')) {
            $query->where('district', $request->district_id);
        }
        
        if ($request->filled('upazila_id')) {
            $query->where('upazila', $request->upazila_id);
        }

        if ($request->filled('blood_group')) {
            $query->whereHas('bloodInventories', function ($q) use ($request) {
                $q->where('blood_group', $request->blood_group)
                  ->where('units_available', '>', 0);
            });
        }

        $bloodBanks = $query->orderBy('name')->paginate(12)->withQueryString();

        $bloodGroups = collect(BloodGroup::cases())->map(fn($bg) => $bg->value);

        return view('public.blood-bank.index', compact(
            'bloodBanks', 'bloodGroups', 'divisions', 'districts', 'upazilas'
        ));
    }

    /**
     * নির্দিষ্ট হাসপাতালের full inventory
     * GET /blood-bank/{organization}
     */
    public function show(Organization $organization)
    {
        if (!$organization->is_blood_bank || $organization->status !== 'verified') {
            abort(404);
        }

        $bloodGroups = collect(BloodGroup::cases())->map(fn($bg) => $bg->value);

        $inventories = BloodInventory::where('organization_id', $organization->id)
            ->get()
            ->keyBy('blood_group');

        // সব ৮টি group ensure করা (missing = 0 units)
        $rows = $bloodGroups->map(function ($group) use ($inventories, $organization) {
            return $inventories->get($group) ?? new BloodInventory([
                'organization_id'        => $organization->id,
                'blood_group'            => $group,
                'units_available'        => 0,
                'is_accepting_donations' => false,
            ]);
        });

        // শেষ আপডেটের সময়
        $lastUpdated = BloodInventory::where('organization_id', $organization->id)
            ->max('updated_at');

        return view('public.blood-bank.show', compact(
            'organization', 'rows', 'lastUpdated'
        ));
    }
}
