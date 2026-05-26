<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ambulance;
use App\Services\GamificationService;
use App\Models\PointLog;

class AmbulanceManagementController extends Controller
{
    public function index(Request $request)
    {
        $pendingAmbulances = Ambulance::with(['division', 'district', 'upazila', 'adder'])
            ->where('status', 'active')
            ->where('is_verified', false)
            ->latest()
            ->paginate(20, ['*'], 'pending_page');
            
        $verifiedAmbulances = Ambulance::with(['division', 'district', 'upazila', 'adder'])
            ->where('status', 'active')
            ->where('is_verified', true)
            ->latest()
            ->paginate(20, ['*'], 'verified_page');
            
        return view('admin.ambulances.index', compact('pendingAmbulances', 'verifiedAmbulances'));
    }
    
    public function verify(Ambulance $ambulance)
    {
        if ($ambulance->is_verified) {
            return back()->with('error', 'অ্যাম্বুলেন্সটি ইতোমধ্যেই ভেরিফাইড।');
        }
        
        $ambulance->update(['is_verified' => true]);
        
        if ($ambulance->added_by) {
            $user = $ambulance->adder;
            if ($user) {
                $user->notify(new \App\Notifications\AmbulanceVerifiedNotification($ambulance));
            }
        }
        
        return back()->with('success', 'অ্যাম্বুলেন্স সফলভাবে ভেরিফাই করা হয়েছে।');
    }
    
    public function destroy(Ambulance $ambulance)
    {
        $ambulance->delete();
        return back()->with('success', 'অ্যাম্বুলেন্স ডিলিট করা হয়েছে।');
    }
}
