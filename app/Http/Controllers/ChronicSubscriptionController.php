<?php

namespace App\Http\Controllers;

use App\Models\ChronicRequestSubscription;
use App\Models\ChronicSubscriptionBuddy;
use App\Models\Hospital;
use App\Models\Division;
use App\Support\PhoneNormalizer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChronicSubscriptionController extends Controller
{
    /**
     * রোগীর সকল subscription তালিকা (Index)
     */
    public function index(Request $request)
    {
        $subscriptions = $request->user()->chronicSubscriptions()
            ->with(['buddies.donor', 'hospital', 'district', 'upazila'])
            ->orderByDesc('is_active')
            ->orderBy('is_paused')
            ->orderBy('next_needed_at')
            ->get();

        return view('chronic.index', compact('subscriptions'));
    }

    /**
     * নতুন subscription form (Create)
     */
    public function create()
    {
        $divisions = Division::all();
        $districts = \App\Models\District::orderBy('name')->get();
        $hospitals = Hospital::where('is_verified', true)->orderBy('name')->get();

        return view('chronic.create', compact('divisions', 'districts', 'hospitals'));
    }

    /**
     * নতুন subscription সংরক্ষণ (Store)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name'    => ['required', 'string', 'max:255'],
            'blood_group'     => ['required', 'string'],
            'component_type'  => ['required', 'string'],
            'condition_type'  => ['required', 'string', 'in:thalassemia,dialysis,sickle_cell,cancer,other'],
            'bags_needed'     => ['required', 'integer', 'min:1', 'max:5'],
            'hospital_id'     => ['nullable', 'exists:hospitals,id'],
            'division_id'     => ['required', 'exists:divisions,id'],
            'district_id'     => ['required', 'exists:districts,id'],
            'upazila_id'      => ['required', 'exists:upazilas,id'],
            'address'         => ['nullable', 'string', 'max:500'],
            'contact_name'    => ['nullable', 'string', 'max:255'],
            'contact_number'  => ['required', 'string', 'max:30'],
            'is_phone_hidden' => ['boolean'],
            'cadence_days'    => ['required', 'integer', 'min:14', 'max:90'],
            'lead_time_days'  => ['required', 'integer', 'min:1', 'max:7'],
            'next_needed_at'  => ['required', 'date', 'after:now'],
            'notes'           => ['nullable', 'string', 'max:1000'],
            'notes_for_donor' => ['nullable', 'string', 'max:500'],
            'urgency'         => ['required', 'string', 'in:normal,urgent,emergency'],
        ], [
            'next_needed_at.after' => 'পরবর্তী রক্তের তারিখ অবশ্যই ভবিষ্যতের কোনো তারিখ হতে হবে।',
        ]);

        $rawPhone = $validated['contact_number'];
        $normalizedPhone = PhoneNormalizer::normalizeBdPhone($rawPhone);

        ChronicRequestSubscription::create(array_merge($validated, [
            'user_id'                   => $request->user()->id,
            'contact_number_normalized' => $normalizedPhone,
            'next_needed_at'            => Carbon::parse($validated['next_needed_at']),
            'is_active'                 => true,
            'is_paused'                 => false,
        ]));

        return redirect()->route('chronic.index')->with('success', '✅ দীর্ঘমেয়াদী সাবস্ক্রিপশন সফলভাবে তৈরি হয়েছে।');
    }

    /**
     * বিস্তারিত দেখা (Show)
     */
    public function show(Request $request, ChronicRequestSubscription $subscription)
    {
        abort_if((int) $subscription->user_id !== (int) $request->user()->id, 403);

        $subscription->load(['buddies.donor', 'hospital', 'district', 'upazila', 'dispatchedRequests' => function ($q) {
            $q->take(5);
        }]);

        return view('chronic.show', compact('subscription'));
    }

    /**
     * সম্পাদনা form (Edit)
     */
    public function edit(Request $request, ChronicRequestSubscription $subscription)
    {
        abort_if((int) $subscription->user_id !== (int) $request->user()->id, 403);

        $divisions = Division::all();
        $districts = \App\Models\District::orderBy('name')->get();
        $hospitals = Hospital::where('is_verified', true)->orderBy('name')->get();

        return view('chronic.edit', compact('subscription', 'divisions', 'districts', 'hospitals'));
    }

    /**
     * সম্পাদনা সংরক্ষণ (Update)
     */
    public function update(Request $request, ChronicRequestSubscription $subscription)
    {
        abort_if((int) $subscription->user_id !== (int) $request->user()->id, 403);

        $validated = $request->validate([
            'patient_name'    => ['required', 'string', 'max:255'],
            'blood_group'     => ['required', 'string'],
            'component_type'  => ['required', 'string'],
            'condition_type'  => ['required', 'string', 'in:thalassemia,dialysis,sickle_cell,cancer,other'],
            'bags_needed'     => ['required', 'integer', 'min:1', 'max:5'],
            'hospital_id'     => ['nullable', 'exists:hospitals,id'],
            'division_id'     => ['required', 'exists:divisions,id'],
            'district_id'     => ['required', 'exists:districts,id'],
            'upazila_id'      => ['required', 'exists:upazilas,id'],
            'address'         => ['nullable', 'string', 'max:500'],
            'contact_name'    => ['nullable', 'string', 'max:255'],
            'contact_number'  => ['required', 'string', 'max:30'],
            'is_phone_hidden' => ['boolean'],
            'cadence_days'    => ['required', 'integer', 'min:14', 'max:90'],
            'lead_time_days'  => ['required', 'integer', 'min:1', 'max:7'],
            'next_needed_at'  => ['required', 'date'],
            'notes'           => ['nullable', 'string', 'max:1000'],
            'notes_for_donor' => ['nullable', 'string', 'max:500'],
            'urgency'         => ['required', 'string', 'in:normal,urgent,emergency'],
        ]);

        $rawPhone = $validated['contact_number'];
        $normalizedPhone = PhoneNormalizer::normalizeBdPhone($rawPhone);

        $subscription->update(array_merge($validated, [
            'contact_number_normalized' => $normalizedPhone,
            'next_needed_at'            => Carbon::parse($validated['next_needed_at']),
        ]));

        return redirect()->route('chronic.show', $subscription->id)->with('success', '✅ সাবস্ক্রিপশন সফলভাবে আপডেট হয়েছে।');
    }

    /**
     * Pause / Resume
     */
    public function pause(Request $request, ChronicRequestSubscription $subscription)
    {
        abort_if((int) $subscription->user_id !== (int) $request->user()->id, 403);

        $action = $request->input('action'); // 'pause' or 'resume'
        $reason = $request->input('reason');
        $pausedUntil = $request->input('paused_until'); // date

        if ($action === 'pause') {
            $subscription->update([
                'is_paused' => true,
                'status_reason' => $reason,
                'paused_until' => $pausedUntil ? Carbon::parse($pausedUntil)->endOfDay() : null,
            ]);
            $msg = 'সাবস্ক্রিপশন সাময়িক বিরতিতে রাখা হয়েছে।';
        } else {
            $subscription->update([
                'is_paused' => false,
                'status_reason' => null,
                'paused_until' => null,
            ]);
            $msg = 'সাবস্ক্রিপশন আবার চালু করা হয়েছে।';
        }

        return back()->with('success', $msg);
    }

    /**
     * Deactivate / Soft Delete
     */
    public function destroy(Request $request, ChronicRequestSubscription $subscription)
    {
        abort_if((int) $subscription->user_id !== (int) $request->user()->id, 403);

        $reason = $request->input('reason', 'User requested deletion');

        $subscription->update([
            'is_active' => false,
            'status_reason' => $reason,
        ]);

        // Also deactivate buddies
        $subscription->buddies()->update(['is_active' => false]);

        return redirect()->route('chronic.index')->with('success', 'সাবস্ক্রিপশন বন্ধ করা হয়েছে।');
    }

    /**
     * Remove Buddy
     */
    public function removeBuddy(Request $request, ChronicRequestSubscription $subscription, ChronicSubscriptionBuddy $buddy)
    {
        abort_if((int) $subscription->user_id !== (int) $request->user()->id, 403);
        abort_if((int) $buddy->subscription_id !== (int) $subscription->id, 404);

        $buddy->delete();

        return back()->with('success', 'Buddy কে সফলভাবে রিমুভ করা হয়েছে। সিস্টেম শিগগিরই নতুন কাউকে খুঁজবে।');
    }
}
