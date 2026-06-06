<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Enums\BloodGroup;
use App\Http\Controllers\Controller;
use App\Models\BloodInventory;
use App\Models\BloodInventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BloodInventoryController extends Controller
{
    /**
     * Blood inventory management page দেখানো।
     * GET /org/inventory
     */
    public function index()
    {
        $org = Auth::user()->organization;

        if (!$org) {
            return redirect()->route('org.dashboard')
                ->with('error', 'আপনার কোনো অর্গানাইজেশন নেই।');
        }

        // সব ৮টি blood group নিশ্চিত করি — missing group-এর জন্য default row
        $bloodGroups = collect(BloodGroup::cases())->map(fn($bg) => $bg->value);

        $inventories = BloodInventory::where('organization_id', $org->id)
            ->get()
            ->keyBy('blood_group');

        // সব group-এর জন্য row ensure করা
        $rows = $bloodGroups->map(function ($group) use ($inventories, $org) {
            return $inventories->get($group) ?? new BloodInventory([
                'organization_id'       => $org->id,
                'blood_group'           => $group,
                'units_available'       => 0,
                'is_accepting_donations' => true,
                'notes'                 => null,
            ]);
        });

        // শেষ ৩০ দিনের log summary (admin analytics)
        $recentLogs = BloodInventoryLog::where('organization_id', $org->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->with('recorder')
            ->get();

        return view('org.inventory.index', compact('org', 'rows', 'recentLogs'));
    }

    /**
     * Bulk stock update — সব group একসাথে save।
     * PUT /org/inventory
     */
    public function update(Request $request)
    {
        $org = Auth::user()->organization;

        if (!$org) {
            return redirect()->route('org.dashboard')
                ->with('error', 'অর্গানাইজেশন পাওয়া যায়নি।');
        }

        $request->validate([
            'inventory'                  => 'required|array',
            'inventory.*.blood_group'    => 'required|string|max:5',
            'inventory.*.units_available' => 'required|integer|min:0|max:9999',
            'notes'                      => 'nullable|string|max:500',
            'is_accepting_donations'     => 'nullable|boolean',
        ]);

        $userId = Auth::id();
        $isAccepting = $request->boolean('is_accepting_donations', true);

        DB::transaction(function () use ($request, $org, $userId, $isAccepting) {
            foreach ($request->input('inventory', []) as $item) {
                $bloodGroup = $item['blood_group'];
                $units      = (int) $item['units_available'];

                // ① blood_inventories — overwrite (current snapshot)
                $inventory = BloodInventory::updateOrCreate(
                    [
                        'organization_id' => $org->id,
                        'blood_group'     => $bloodGroup,
                    ],
                    [
                        'units_available'        => $units,
                        'is_accepting_donations' => $isAccepting,
                        'notes'                  => $request->input('notes'),
                    ]
                );

                // ② blood_inventory_logs — append (Time-Series Ledger)
                BloodInventoryLog::create([
                    'organization_id' => $org->id,
                    'blood_group'     => $bloodGroup,
                    'units'           => $units,
                    'action'          => 'manual_update',
                    'recorded_by'     => $userId,
                ]);
            }

            // org-কে blood bank হিসেবে mark করা (একবারই)
            if (!$org->is_blood_bank) {
                $org->update(['is_blood_bank' => true]);
            }
        });

        return redirect()->route('org.inventory.index')
            ->with('success', 'ব্লাড ব্যাংক ইনভেন্টরি সফলভাবে আপডেট হয়েছে!');
    }

    /**
     * "আজ Donation নিচ্ছি / নিচ্ছি না" quick toggle।
     * POST /org/inventory/toggle
     */
    public function toggleAccepting(Request $request)
    {
        $org = Auth::user()->organization;

        if (!$org) {
            return response()->json(['success' => false, 'message' => 'অর্গানাইজেশন পাওয়া যায়নি।'], 404);
        }

        // Get intended state from frontend
        $newValue = $request->boolean('state');

        // সব blood group-এর জন্য একসাথে toggle
        BloodInventory::where('organization_id', $org->id)
            ->update(['is_accepting_donations' => $newValue]);

        return response()->json([
            'success'    => true,
            'accepting'  => $newValue,
            'message'    => $newValue
                ? 'আজ রক্ত সংগ্রহ করা হচ্ছে।'
                : 'আজ রক্ত সংগ্রহ বন্ধ রাখা হয়েছে।',
        ]);
    }
}
