<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodRequestRequest;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
// 👇 নিশ্চিত করো এই লাইনটি আছে
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
        // পলিসিতে মেথডের নাম 'markFulfilled' তাই এখানেও সেটি ব্যবহার করা হয়েছে
        Gate::authorize('markFulfilled', $bloodRequest);

        $bloodRequest->update([
            'status' => 'fulfilled',
        ]);

        return back()->with('success', 'অভিনন্দন! রিকোয়েস্টটি সম্পন্ন (Fulfilled) মার্ক করা হয়েছে।');
    }

    public function show(BloodRequest $bloodRequest)
    {
        $bloodRequest->load('requester:id,name');
        return view('requests.show', ['request' => $bloodRequest]);
    }
}