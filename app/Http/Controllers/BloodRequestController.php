<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodRequestRequest;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BloodRequestController extends Controller
{
    /**
     * সকল পেন্ডিং রিকোয়েস্ট দেখায় (Eager Loading সহ)
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $requests = BloodRequest::query()
            ->with([
                'requester:id,name', 
                'responses' => fn ($q) => $q->where('user_id', $userId),
            ])
            ->where('status', 'pending')
            ->orderByRaw('needed_at is null asc')
            ->orderBy('needed_at')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        return view('requests.create');
    }

    /**
     * নতুন রিকোয়েস্ট তৈরি করা
     */
    public function store(StoreBloodRequestRequest $request)
    {
        $data = $request->validated();
        
        $data['requested_by'] = $request->user()->id;
        $data['status'] = 'pending';

        BloodRequest::create($data);

        return redirect()->route('requests.index')->with('success', 'আপনার রক্তের রিকোয়েস্টটি সফলভাবে তৈরি হয়েছে।');
    }

    /**
     * রিকোয়েস্টটি Fulfilled মার্ক করা (Security Enforced)
     */
    public function fulfill(Request $request, BloodRequest $bloodRequest)
    {
        // মডার্ন লারাভেল স্ট্যান্ডার্ড অনুযায়ী গেট অথোরাইজেশন
        Gate::authorize('markFulfilled', $bloodRequest);

        $bloodRequest->update([
            'status' => 'fulfilled',
        ]);

        return back()->with('success', 'অভিনন্দন! রিকোয়েস্টটি সম্পন্ন (Fulfilled) মার্ক করা হয়েছে।');
    }

    /**
     * রিকোয়েস্ট ডিটেইলস এবং এক্সেপ্টেড ডোনার লিস্ট দেখানো
     */
    public function show(\Illuminate\Http\Request $httpRequest, \App\Models\BloodRequest $bloodRequest)
    {
        // ❌ $this->authorize('view', $bloodRequest); // এটি এরর দেবে
        // ✅ সঠিক পদ্ধতি:
        Gate::authorize('view', $bloodRequest);

        // IMPORTANT: always load responses for this bloodRequest
        $bloodRequest->load([
            'requester:id,name',
            'responses.user:id,name,phone',
        ]);

        // values() ব্যবহার করার ফলে কালেকশনের ইনডেক্সগুলো নতুন করে 0, 1, 2 তে সাজানো হবে
        $accepted = $bloodRequest->responses->where('status', 'accepted')->values();
        $declined = $bloodRequest->responses->where('status', 'declined')->values();

        $canViewAcceptedDonors = $httpRequest->user()->can('viewAcceptedDonors', $bloodRequest);

        return view('requests.show', [
            // 🚨 'request' এর বদলে 'bloodRequest' নাম দিলাম যাতে কনফ্লিক্ট না হয়
            'bloodRequest' => $bloodRequest, 
            'acceptedCount' => $accepted->count(),
            'declinedCount' => $declined->count(),
            'acceptedResponses' => $canViewAcceptedDonors ? $accepted : collect(),
            'canViewAcceptedDonors' => $canViewAcceptedDonors,
        ]);
    }
}