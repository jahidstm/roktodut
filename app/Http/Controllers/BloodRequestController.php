<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodRequestRequest;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BloodRequestController extends Controller
{
    /**
     * সকল পেন্ডিং রিকোয়েস্ট দেখায় (Eager Loading এবং Filtering সহ)
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $requests = BloodRequest::query()
            ->with([
                'requester:id,name', 
                'responses' => fn ($q) => $q->where('user_id', $userId),
            ])
            // 🚨 এই লাইনটিই ম্যাজিক! Fulfilled রিকোয়েস্টগুলো অটো হাইড হয়ে যাবে
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
        // পলিসি এনফোর্সমেন্ট: তুমি markFulfilled ব্যবহার করেছো, যা অত্যন্ত প্রফেশনাল
        Gate::authorize('markFulfilled', $bloodRequest);

        $bloodRequest->update([
            'status' => 'fulfilled',
        ]);

        return back()->with('success', 'অভিনন্দন! রিকোয়েস্টটি সম্পন্ন (Fulfilled) মার্ক করা হয়েছে।');
    }

    /**
     * রিকোয়েস্ট ডিটেইলস এবং এক্সেপ্টেড ডোনার লিস্ট দেখানো
     */
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