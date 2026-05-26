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
    
    public function verify(Ambulance $ambulance, GamificationService $gamificationService)
    {
        if ($ambulance->is_verified) {
            return back()->with('error', 'অ্যাম্বুলেন্সটি ইতোমধ্যেই ভেরিফাইড।');
        }
        
        $ambulance->update(['is_verified' => true]);
        
        // Award points if added by a user
        if ($ambulance->added_by) {
            $user = $ambulance->adder;
            if ($user) {
                $gamificationService->awardPoints(
                    user: $user,
                    points: 20,
                    actionType: PointLog::ACTION_AMBULANCE_VERIFIED,
                    metadata: ['ambulance_id' => $ambulance->id]
                );
                
                $user->notify(new \App\Notifications\GamificationRewardNotification(
                    title: '✅ অ্যাম্বুলেন্স ভেরিফাইড!',
                    message: "আপনার সাবমিট করা অ্যাম্বুলেন্স '{$ambulance->name}' ভেরিফাই হয়েছে। স্পেশাল অবদান রাখার জন্য আপনি ২০ পয়েন্ট পেয়েছেন!",
                    points: 20
                ));
            }
        }
        
        return back()->with('success', 'অ্যাম্বুলেন্স সফলভাবে ভেরিফাই করা হয়েছে এবং ইউজারকে পয়েন্ট দেওয়া হয়েছে।');
    }
    
    public function destroy(Ambulance $ambulance)
    {
        $ambulance->delete();
        return back()->with('success', 'অ্যাম্বুলেন্স ডিলিট করা হয়েছে।');
    }
}
