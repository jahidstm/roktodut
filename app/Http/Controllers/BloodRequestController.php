<?php

namespace App\Http\Controllers;

use App\Events\DonationCompleted;
use App\Http\Requests\StoreBloodRequestRequest;
use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use App\Models\District;
use App\Models\User;
use App\Models\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class BloodRequestController extends Controller
{
    /**
     * সকল পেন্ডিং রিকোয়েস্ট দেখায় (Advanced Filtering ও Eager Loading সহ)
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = BloodRequest::query()
            ->with([
                'requester:id,name',
                'responses' => fn($q) => $q->where('user_id', $userId),
                'district:id,name', // 🚀 ইগার লোডিং (পারফরম্যান্স অপ্টিমাইজেশন)
                'upazila:id,name'
            ])
            ->where('status', 'pending');

        // 🎯 স্মার্ট ফিল্টারিং লজিক (Relational IDs)
        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('upazila_id')) {
            $query->where('upazila_id', $request->upazila_id);
        }

        $requests = $query->orderByRaw('needed_at is null asc')
            ->orderBy('needed_at')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('requests.index', compact('requests'));
    }

    /**
     * রিকোয়েস্ট তৈরির ফর্ম দেখায়
     */
    public function create()
    {
        return view('requests.create');
    }

    /**
     * নতুন রিকোয়েস্ট সেভ করা এবং ডোনারদের নোটিফাই করা
     */
    public function store(StoreBloodRequestRequest $request)
    {
        $data = $request->validated();

        $data['requested_by'] = $request->user()->id;
        $data['status'] = 'pending';

        // ১. রিকোয়েস্ট সেভ করা
        $bloodRequest = BloodRequest::create($data);

        // ২. জেলার নাম বের করা (নোটিফিকেশনে দেখানোর জন্য)
        $districtName = District::find($bloodRequest->district_id)->name ?? 'আপনার';

        // ⚙️ ৩. স্মার্ট ডোনার ম্যাচিং অ্যালগরিদম (The Active Engine)
        $eligibleCutoff = Carbon::now()->subDays(120); // ১২০ দিনের জৈবিক কুলডাউন

        // ওই জেলার ডোনারদের খুঁজে বের করা (is_verified কলামটি রিমুভ করা হয়েছে)
        $matchingDonors = User::where('blood_group', $bloodRequest->blood_group)
            ->where('district_id', $bloodRequest->district_id)
            ->where('id', '!=', $bloodRequest->requested_by)
            ->whereIn('role', ['donor', 'org_admin'])
            ->where(function ($q) use ($eligibleCutoff) {
                $q->whereNull('last_donation_date')
                    ->orWhere('last_donation_date', '<=', $eligibleCutoff);
            })
            ->get();

        // 🚀 ৪. টার্গেটেড নোটিফিকেশন পাঠানো
        if ($matchingDonors->count() > 0) {
            $notifications = [];
            foreach ($matchingDonors as $donor) {
                $notifications[] = [
                    'user_id' => $donor->id,
                    'title'   => 'জরুরি রক্তের প্রয়োজন!',
                    'message' => "{$districtName} জেলায় {$bloodRequest->blood_group} রক্তের জন্য একটি ইমার্জেন্সি রিকোয়েস্ট এসেছে।",
                    'link'    => route('requests.show', $bloodRequest->id),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            CustomNotification::insert($notifications);
        }

        return redirect()->route('requests.index')
            ->with('success', 'আপনার রক্তের রিকোয়েস্টটি সফলভাবে তৈরি হয়েছে এবং ' . $matchingDonors->count() . ' জন ডোনারকে অ্যালার্ট পাঠানো হয়েছে।');
    }

    /**
     * রিকোয়েস্ট সম্পন্ন (Fulfilled) মার্ক করা
     * → DonationCompleted ইভেন্ট ফায়ার করা হয় প্রতিটি accepted ডোনারের জন্য
     */
    public function fulfill(Request $request, BloodRequest $bloodRequest)
    {
        Gate::authorize('markFulfilled', $bloodRequest);

        $bloodRequest->update(['status' => 'fulfilled']);

        // ─── সকল accepted ডোনারদের জন্য Event Fire করা ────────────────────
        $acceptedResponses = BloodRequestResponse::where('blood_request_id', $bloodRequest->id)
            ->where('status', 'accepted')
            ->with('user')
            ->get();

        foreach ($acceptedResponses as $response) {
            $donor = $response->user;
            if (!$donor) continue;

            // 🎯 First Responder Detection:
            // ইমার্জেন্সি রিকোয়েস্টে ৩ ঘণ্টার মধ্যে রেসপন্ড করলে বোনাস
            $isEmergency = ($bloodRequest->urgency === 'emergency');
            $responseTimeHours = $bloodRequest->created_at->diffInHours($response->created_at);
            $isFirstResponder  = $isEmergency && $responseTimeHours <= 3;

            // 🚀 ইভেন্ট ফায়ার — RewardDonorPoints Listener ব্যাকগ্রাউন্ডে চলবে
            event(new DonationCompleted(
                donor:            $donor,
                bloodRequest:     $bloodRequest,
                isEmergency:      $isEmergency,
                isFirstResponder: $isFirstResponder,
            ));
        }

        return back()->with('success', '🎉 অভিনন্দন! রিকোয়েস্টটি সম্পন্ন মার্ক করা হয়েছে এবং ডোনাররা পয়েন্ট পাবেন।');
    }

    /**
     * রিকোয়েস্টের বিস্তারিত এবং এক্সেপ্টেড ডোনার লিস্ট দেখানো
     */
    public function show(Request $request, BloodRequest $bloodRequest)
    {
        Gate::authorize('view', $bloodRequest);

        // 🚀 রিলেশনাল ডেটা লোড করা (JSON এরর ফিক্স করার জন্য)
        $bloodRequest->load([
            'requester:id,name',
            'responses.user:id,name,phone',
            'district:id,name',
            'upazila:id,name'
        ]);

        $accepted = $bloodRequest->responses->where('status', 'accepted')->values();
        $declined = $bloodRequest->responses->where('status', 'declined')->values();

        $canViewAcceptedDonors = $request->user()->can('viewAcceptedDonors', $bloodRequest);

        return view('requests.show', [
            'bloodRequest' => $bloodRequest,
            'acceptedCount' => $accepted->count(),
            'declinedCount' => $declined->count(),
            'acceptedResponses' => $canViewAcceptedDonors ? $accepted : collect(),
            'canViewAcceptedDonors' => $canViewAcceptedDonors,
        ]);
    }
}
