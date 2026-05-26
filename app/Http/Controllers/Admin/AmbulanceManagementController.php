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
        $status = $request->query('status', 'pending');
        
        $query = Ambulance::with(['division', 'district', 'upazila', 'adder'])
            ->where('status', 'active');
            
        if ($status === 'pending') {
            $query->where('is_verified', false);
        } elseif ($status === 'verified') {
            $query->where('is_verified', true);
        }
        
        $ambulances = $query->latest()->paginate(20);
        
        return view('admin.ambulances.index', compact('ambulances', 'status'));
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
