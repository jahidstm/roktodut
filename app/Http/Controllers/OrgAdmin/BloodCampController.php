<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\BloodCamp;
use App\Models\CampRegistration;
use App\Models\CampAttendance;
use App\Models\User;
use App\Models\PointLog;
use App\Models\Badge;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BloodCampController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        if (!$admin->organization_id) abort(403);

        $camps = BloodCamp::with(['district:id,name,bn_name', 'upazila:id,name'])
            ->where('organization_id', $admin->organization_id)
            ->withCount([
                'registrations as registered_count' => fn ($q) => $q->where('status', 'registered'),
                'registrations as checked_in_count' => fn ($q) => $q->whereIn('status', ['checked_in', 'completed']),
                'attendances as attendance_count',
            ])
            ->latest()
            ->paginate(10);

        return view('org.camps.index', compact('camps'));
    }

    public function create()
    {
        $districts = District::orderBy('name')->get(['id', 'name']);
        return view('org.camps.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $admin = Auth::user();
        if (!$admin->organization_id) {
            abort(403, 'আপনার কোনো অর্গানাইজেশন অ্যাসাইন করা নেই।');
        }

        $validated = $this->validateCampPayload($request);
        $status = $this->resolveCampStatus($request);

        BloodCamp::create([
            'organization_id' => $admin->organization_id,
            'name' => $validated['name'],
            'camp_date' => $validated['start_at'],
            'location' => $validated['address_line'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'district_id' => $validated['district_id'],
            'upazila_id' => $validated['upazila_id'],
            'address_line' => $validated['address_line'],
            'contact_name' => $validated['contact_name'],
            'contact_phone' => $validated['contact_phone'],
            'notes' => $validated['notes'] ?? null,
            'target_donors' => $validated['target_donors'] ?? null,
            'is_public' => (bool) ($validated['is_public'] ?? false),
            'status' => $status,
            'created_by' => $admin->id,
        ]);

        $message = $status === 'published'
            ? 'ক্যাম্প সফলভাবে পাবলিশ করা হয়েছে।'
            : 'ক্যাম্প ড্রাফট হিসেবে সংরক্ষণ করা হয়েছে।';

        return redirect()->route('org.camps.index')->with('success', $message);
    }

    public function edit(BloodCamp $camp)
    {
        $admin = Auth::user();
        if ($camp->organization_id !== $admin->organization_id) abort(403);

        $districts = District::orderBy('name')->get(['id', 'name']);
        return view('org.camps.edit', compact('camp', 'districts'));
    }

    public function update(Request $request, BloodCamp $camp)
    {
        $admin = Auth::user();
        if ($camp->organization_id !== $admin->organization_id) abort(403);

        $validated = $this->validateCampPayload($request);
        $status = $this->resolveCampStatus($request, $camp->status);

        $camp->update([
            'name' => $validated['name'],
            'camp_date' => $validated['start_at'],
            'location' => $validated['address_line'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'district_id' => $validated['district_id'],
            'upazila_id' => $validated['upazila_id'],
            'address_line' => $validated['address_line'],
            'contact_name' => $validated['contact_name'],
            'contact_phone' => $validated['contact_phone'],
            'notes' => $validated['notes'] ?? null,
            'target_donors' => $validated['target_donors'] ?? null,
            'is_public' => (bool) ($validated['is_public'] ?? false),
            'status' => $status,
        ]);

        $message = $status === 'published'
            ? 'ক্যাম্প আপডেট করে পাবলিশ করা হয়েছে।'
            : 'ক্যাম্প ড্রাফট হিসেবে আপডেট হয়েছে।';

        return redirect()->route('org.camps.index')->with('success', $message);
    }

    public function publish(BloodCamp $camp)
    {
        $admin = Auth::user();
        if ($camp->organization_id !== $admin->organization_id) abort(403);

        if (!$camp->start_at || !$camp->end_at || !$camp->district_id || !$camp->upazila_id || !$camp->address_line) {
            return back()->with('error', 'ক্যাম্প পাবলিশ করার আগে সব বাধ্যতামূলক তথ্য পূরণ করুন।');
        }

        $camp->update(['status' => 'published']);

        return back()->with('success', 'ক্যাম্প সফলভাবে পাবলিশ করা হয়েছে।');
    }

    public function cancel(BloodCamp $camp)
    {
        $admin = Auth::user();
        if ($camp->organization_id !== $admin->organization_id) abort(403);

        $camp->update(['status' => 'cancelled']);

        return back()->with('success', 'ক্যাম্প বাতিল করা হয়েছে।');
    }

    public function show(BloodCamp $camp)
    {
        $admin = Auth::user();
        if ($camp->organization_id !== $admin->organization_id) abort(403);

        $camp->load(['attendances.user']);
        $camp->loadCount([
            'registrations as registered_count' => fn ($q) => $q->where('status', 'registered'),
            'registrations as checked_in_count' => fn ($q) => $q->whereIn('status', ['checked_in', 'completed']),
        ]);

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

            CampRegistration::updateOrCreate(
                ['camp_id' => $camp->id, 'user_id' => $donor->id],
                ['status' => 'checked_in']
            );

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
            $donor->last_donated_at = $camp->start_at ?? $camp->camp_date;
            
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

    private function validateCampPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'district_id' => ['required', 'exists:districts,id'],
            'upazila_id' => [
                'required',
                Rule::exists('upazilas', 'id')->where(fn ($query) => $query->where('district_id', $request->input('district_id'))),
            ],
            'address_line' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:120'],
            'contact_phone' => ['required', 'regex:/^(\\+8801|01)[3-9]\\d{8}$/'],
            'notes' => ['nullable', 'string'],
            'target_donors' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'is_public' => ['nullable', 'boolean'],
            'submit_action' => ['nullable', 'in:draft,publish'],
        ], [
            'end_at.after' => 'শেষ সময় অবশ্যই শুরুর সময়ের পরে হতে হবে।',
            'contact_phone.regex' => 'সঠিক বাংলাদেশি মোবাইল নম্বর দিন (যেমন: 01XXXXXXXXX বা +8801XXXXXXXXX)।',
            'district_id.required' => 'জেলা নির্বাচন করা আবশ্যক।',
            'upazila_id.required' => 'উপজেলা নির্বাচন করা আবশ্যক।',
        ]);
    }

    private function resolveCampStatus(Request $request, ?string $fallback = null): string
    {
        $action = $request->input('submit_action');

        if ($action === 'publish') {
            return 'published';
        }

        if ($action === 'draft') {
            return 'draft';
        }

        return $fallback ?: 'draft';
    }
}
