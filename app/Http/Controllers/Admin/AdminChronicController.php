<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChronicRequestSubscription;
use Illuminate\Http\Request;

class AdminChronicController extends Controller
{
    public function index(Request $request)
    {
        $query = ChronicRequestSubscription::with(['user', 'district']);

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->where('is_paused', false);
            } elseif ($request->status === 'paused') {
                $query->where('is_active', true)->where('is_paused', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        $subscriptions = $query->latest()->paginate(20);

        return view('admin.chronic.index', compact('subscriptions'));
    }

    public function show(ChronicRequestSubscription $subscription)
    {
        $subscription->load(['user', 'buddies.donor', 'hospital', 'district', 'upazila', 'dispatchedRequests']);
        return view('admin.chronic.show', compact('subscription'));
    }

    public function toggleActive(Request $request, ChronicRequestSubscription $subscription)
    {
        $action = $request->input('action'); // 'activate', 'deactivate'
        
        if ($action === 'activate') {
            $subscription->update([
                'is_active' => true,
                'status_reason' => 'Admin force activated',
            ]);
            $msg = 'সাবস্ক্রিপশন সক্রিয় করা হয়েছে।';
        } else {
            $subscription->update([
                'is_active' => false,
                'status_reason' => 'Admin force deactivated',
            ]);
            $msg = 'সাবস্ক্রিপশন নিষ্ক্রিয় করা হয়েছে।';
        }

        return back()->with('success', $msg);
    }
}
