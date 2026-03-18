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

        BloodRequest::create($data);

        return redirect()->route('requests.index')->with('success', 'আপনার রক্তের রিকোয়েস্টটি সফলভাবে তৈরি হয়েছে।');
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