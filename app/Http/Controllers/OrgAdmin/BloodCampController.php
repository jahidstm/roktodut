<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\BloodCamp;
use App\Models\CampAttendance;
use App\Models\User;
use App\Models\PointLog;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BloodCampController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        if (!$admin->organization_id) abort(403);

        $camps = BloodCamp::where('organization_id', $admin->organization_id)
            ->latest()
            ->paginate(10);

        return view('org.camps.index', compact('camps'));
    }

    public function create()
    {
        return view('org.camps.create');
    }

    public function store(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'camp_date' => 'required|date',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        BloodCamp::create([
            'organization_id' => $admin->organization_id,
            'name' => $request->name,
            'camp_date' => $request->camp_date,
            'location' => $request->location,
            'notes' => $request->notes,
            'created_by' => $admin->id,
        ]);

        return redirect()->route('org.camps.index')->with('success', 'রক্তদান ক্যাম্প সফলভাবে তৈরি করা হয়েছে।');
    }

    public function show(BloodCamp $camp)
    {
        $admin = Auth::user();
        if ($camp->organization_id !== $admin->organization_id) abort(403);

        $camp->load(['attendances.user']);

        // Only approved members of THIS organization can be selected
        $members = User::where('organization_id', $admin->organization_id)
            ->where('role', 'donor')
            ->where('nid_status', 'verified')
            ->get();

        return view('org.camps.show', compact('camp', 'members'));
    }

    public function logAttendance(Request $request, BloodCamp $camp)
    {
        $admin = Auth::user();
        if ($camp->organization_id !== $admin->organization_id) abort(403);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $donor = User::findOrFail($request->user_id);

        if ($donor->organization_id !== $admin->organization_id || $donor->nid_status !== 'verified') {
            return back()->with('error', 'অকার্যকর ডোনার নির্বাচন।');
        }

        // Check duplicates
        if (CampAttendance::where('blood_camp_id', $camp->id)->where('user_id', $donor->id)->exists()) {
            return back()->with('error', 'এই ডোনার ইতিমধ্যেই এই ক্যাম্পে রক্তদান করেছেন।');
        }

        DB::transaction(function () use ($donor, $camp) {
            // 1. Record Attendance
            CampAttendance::create([
                'blood_camp_id' => $camp->id,
                'user_id' => $donor->id,
                'points_awarded' => 100 // 100 points for camp donation
            ]);

            // 2. Add Points (Ledger)
            PointLog::create([
                'user_id' => $donor->id,
                'points' => 100,
                'action_type' => 'camp_donation',
                'metadata' => ['camp_id' => $camp->id, 'camp_name' => $camp->name]
            ]);

            $donor->increment('points', 100);
            $donor->increment('total_donations'); // Just incrementing total for logging
            $donor->increment('total_verified_donations');
            $donor->last_donated_at = $camp->camp_date;
            
            // Monthly points sync
            $currentMonth = now()->format('Y-m');
            if ($donor->monthly_points_month !== $currentMonth) {
                $donor->monthly_points = 100;
                $donor->monthly_points_month = $currentMonth;
            } else {
                $donor->increment('monthly_points', 100);
            }

            $donor->save();

            // 3. Award 'camp_donor' Badge on First Camp
            $campCount = CampAttendance::where('user_id', $donor->id)->count();
            if ($campCount === 1) {
                $badge = Badge::firstOrCreate(
                    ['name' => 'camp_donor'],
                    ['display_name' => 'ক্যাম্প ডোনার', 'description' => 'রক্তদান ক্যাম্পে অংশগ্রহণকারী']
                );
                
                if (!$donor->badges()->where('badge_id', $badge->id)->exists()) {
                    $donor->badges()->attach($badge->id, [
                        'earned_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        });

        return back()->with('success', 'ক্যাম্পে ডোনারের উপস্থিতি সফলভাবে লগ করা হয়েছে।');
    }
}
