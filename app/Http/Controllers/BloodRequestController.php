<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodRequestRequest;
use App\Models\BloodRequest;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = BloodRequest::query()
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

    public function store(StoreBloodRequestRequest $request)
    {
        $data = $request->validated();
        $data['requested_by'] = $request->user()->id;
        $data['status'] = 'pending';

        BloodRequest::create($data);

        return redirect()->route('requests.index')->with('success', 'রিকোয়েস্ট তৈরি হয়েছে।');
    }

    public function fulfill(Request $request, BloodRequest $bloodRequest)
    {
        // যদি policy implement করা থাকে, এটা আনকমেন্ট করো
        // $this->authorize('markFulfilled', $bloodRequest);

        // Minimal rule: already fulfilled হলে no-op
        if ($bloodRequest->status === 'fulfilled') {
            return back()->with('success', 'রিকোয়েস্ট ইতোমধ্যে fulfilled করা আছে।');
        }

        $bloodRequest->update([
            'status' => 'fulfilled',
        ]);

        return back()->with('success', 'রিকোয়েস্ট Fulfilled করা হয়েছে।');
    }

    public function show(BloodRequest $request)
    {
        return view('requests.show', ['request' => $request]);
    }
}