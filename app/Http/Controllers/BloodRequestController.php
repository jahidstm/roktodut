<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodRequestRequest;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
                'responses' => fn ($q) => $q->where('user_id', $userId),
            ])
            ->where('status', 'pending');

        // 🎯 স্মার্ট ফিল্টারিং লজিক (First-principles approach)
        $query->when($request->filled('blood_group'), function ($q) use ($request) {
            $q->where('blood_group', $request->blood_group);
        });

        $query->when($request->filled('district'), function ($q) use ($request) {
            $q->where('district', 'like', '%' . $request->district . '%');
        });

        $query->when($request->filled('thana'), function ($q) use ($request) {
            $q->where('thana', 'like', '%' . $request->thana . '%');
        });

        $requests = $query->orderByRaw('needed_at is null asc')
            ->orderBy('needed_at')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString(); // 🚨 পেজিনেশনের সাথে ফিল্টার ডেটা ধরে রাখার জন্য ম্যান্ডেটরি

        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        return view('requests.create');
    }

    public function store(StoreBloodRequestRequest $request)
    {
        $data = $request->validated();
        
        $data['requested_by'] = $request->user()->id;
        $data['status'] = 'pending';

        // ১. রিকোয়েস্ট সেভ করা
        $bloodRequest = BloodRequest::create($data);

        // ⚙️ ২. স্মার্ট ডোনার ম্যাচিং অ্যালগরিদম (The Active Engine)
        $ninetyDaysAgo = \Carbon\Carbon::now()->subDays(90);

        // ওই জেলার ভেরিফাইড এবং এভেইলেবল ডোনারদের খুঁজে বের করা
        $matchingDonors = \App\Models\User::where('blood_group', $bloodRequest->blood_group)
            ->where('district', $bloodRequest->district)
            ->where('id', '!=', $bloodRequest->requested_by) // যে রিকোয়েস্ট করেছে তাকে নোটিফিকেশন দেব না
            ->where('is_verified', true)
            ->whereIn('role', ['donor', 'org_admin'])
            ->where(function ($q) use ($ninetyDaysAgo) {
                $q->whereNull('last_donation_date')
                  ->orWhere('last_donation_date', '<=', $ninetyDaysAgo);
            })
            ->get();

        // 🚀 ৩. টার্গেটেড নোটিফিকেশন পাঠানো (Mass Assignment)
        if ($matchingDonors->count() > 0) {
            $notifications = [];
            foreach ($matchingDonors as $donor) {
                $notifications[] = [
                    'user_id' => $donor->id,
                    'title'   => 'জরুরি রক্তের প্রয়োজন!',
                    'message' => "আপনার জেলায় ({$bloodRequest->district}) {$bloodRequest->blood_group} রক্তের জন্য একটি ইমার্জেন্সি রিকোয়েস্ট এসেছে।",
                    'link'    => route('requests.show', $bloodRequest->id),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // একবারে সব নোটিফিকেশন ডাটাবেসে ইনসার্ট করা (Performance Optimized)
            \App\Models\CustomNotification::insert($notifications);
        }

        return redirect()->route('requests.index')
            ->with('success', 'আপনার রক্তের রিকোয়েস্টটি সফলভাবে তৈরি হয়েছে এবং ' . $matchingDonors->count() . ' জন ডোনারকে অ্যালার্ট পাঠানো হয়েছে।');
    }

    public function fulfill(Request $request, BloodRequest $bloodRequest)
    {
        Gate::authorize('markFulfilled', $bloodRequest);

        $bloodRequest->update([
            'status' => 'fulfilled',
        ]);

        return back()->with('success', 'অভিনন্দন! রিকোয়েস্টটি সম্পন্ন (Fulfilled) মার্ক করা হয়েছে।');
    }

    public function show(Request $request, BloodRequest $bloodRequest)
    {
        Gate::authorize('view', $bloodRequest);

        $bloodRequest->load([
            'requester:id,name',
            'responses.user:id,name,phone',
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