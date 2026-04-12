<?php

namespace App\Http\Controllers;

use App\Models\BloodRequestResponse;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DonationClaimController extends Controller
{
    public function __construct(private GamificationService $gamification)
    {
    }

    /**
     * ডোনারের ডোনেশন ক্লেইম হ্যান্ডেল করা (PIN or Image)
     */
    public function store(Request $request, BloodRequestResponse $response)
    {
        // 🛡️ সিকিউরিটি চেক
        if ($response->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'claim_method' => 'required|in:pin,image',
            'pin'          => 'required_if:claim_method,pin|nullable|string|size:4',
            'proof_image'  => 'required_if:claim_method,image|nullable|image|max:2048',
        ]);

        if ($request->claim_method === 'pin') {
            if ($request->pin === $response->verification_pin) {

                $response->update([
                    'verification_status' => 'claimed',
                    'donor_claimed_at'    => now(),
                ]);

                return back()->with('success', '🎉 পিন মিলেছে! আপনার দাবিটি (Claim) রেকর্ড করা হয়েছে। গ্রহীতা বা অ্যাডমিন রিভিউয়ের পর আপনার পয়েন্ট ও ব্যাজ যুক্ত হবে।');
            }
            return back()->with('error', 'দুঃখিত, পিনটি সঠিক নয়।');
        }

        if ($request->claim_method === 'image') {
            $path = $request->file('proof_image')->store('donation_proofs', 'private');
            $response->update([
                'proof_image_path'    => $path,
                'verification_status' => 'claimed',
                'donor_claimed_at'    => now(),
            ]);

            return back()->with('success', '📸 আপনার প্রমাণটি জমা হয়েছে। যাচাইয়ের পর পয়েন্ট ও ব্যাজ যুক্ত হবে।');
        }
    }

    /**
     * রোগীর লোকের পক্ষ থেকে ভেরিফিকেশন + Recipient Review পয়েন্ট
     */
    public function verifyByRecipient(Request $request, BloodRequestResponse $response)
    {
        if ($response->bloodRequest->requested_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['decision' => 'required|in:approve,dispute']);

        if ($request->decision === 'approve') {
            $response->update([
                'verification_status' => 'verified',
                'fulfilled_at'        => now(),
                'fulfilled_by'        => Auth::id()
            ]);

            $donor = $response->user;
            if ($donor) {
                $bloodRequest = $response->bloodRequest;
                $isFirstResponder = false;
                $isMidnightSavior = false;
                
                if ($bloodRequest && $bloodRequest->urgency === 'emergency') {
                    $responseTimeHours = $bloodRequest->created_at->diffInHours($response->created_at);
                    if ($responseTimeHours <= 3) {
                        $isFirstResponder = true;
                    }

                    $responseHour = $response->created_at->hour;
                    if ($responseHour >= 0 && $responseHour <= 5) {
                        $isMidnightSavior = true;
                    }
                }

                $this->gamification->processDonationReward(
                    donor: $donor,
                    bloodRequest: $bloodRequest ?? new \App\Models\BloodRequest(),
                    isFirstResponder: $isFirstResponder,
                    isMidnightSavior: $isMidnightSavior
                );

                // 🎯 গ্রহীতার রিভিউ পয়েন্ট (+১০)
                $this->gamification->awardReviewPoints($donor);
            }

            return back()->with('success', '✅ ডোনারকে সফলভাবে ভেরিফাই করা হয়েছে। তাকে পয়েন্ট ও ব্যাজ দেওয়া হয়েছে।');
        }

        if ($request->decision === 'dispute') {
            $response->update(['verification_status' => 'disputed']);
            return back()->with('error', 'অভিযোগটি গ্রহণ করা হয়েছে।');
        }
    }

    /**
     * 🛡️ অ্যাডমিনের ম্যানুয়াল ভেরিফিকেশন
     */
    public function adminVerify(Request $request, BloodRequestResponse $response)
    {
        $user     = Auth::user();
        $userRole = $user->role->value ?? $user->role;

        if ($userRole !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['status' => 'required|in:verified,rejected']);

        if ($request->status === 'verified') {
            $response->update([
                'verification_status' => 'verified',
                'fulfilled_at'        => now(),
                'fulfilled_by'        => Auth::id()
            ]);

            $donor = $response->user;
            if ($donor) {
                $bloodRequest = $response->bloodRequest;
                $isFirstResponder = false;
                $isMidnightSavior = false;
                
                if ($bloodRequest && $bloodRequest->urgency === 'emergency') {
                    $responseTimeHours = $bloodRequest->created_at->diffInHours($response->created_at);
                    if ($responseTimeHours <= 3) {
                        $isFirstResponder = true;
                    }

                    $responseHour = $response->created_at->hour;
                    if ($responseHour >= 0 && $responseHour <= 5) {
                        $isMidnightSavior = true;
                    }
                }

                $this->gamification->processDonationReward(
                    donor: $donor,
                    bloodRequest: $bloodRequest ?? new \App\Models\BloodRequest(),
                    isFirstResponder: $isFirstResponder,
                    isMidnightSavior: $isMidnightSavior
                );
            }

            return back()->with('success', '✅ অ্যাপ্রুভ করা হয়েছে। ডোনারকে পয়েন্ট ও ব্যাজ দেওয়া হয়েছে।');
        }

        if ($request->status === 'rejected') {
            $response->update(['verification_status' => 'rejected']);
            return back()->with('error', 'বাতিল করা হয়েছে।');
        }
    }

    /**
     * সিকিউরলি প্রুফ ইমেজ দেখার জন্য
     */
    public function viewProof(Request $request, BloodRequestResponse $response)
    {
        $user = Auth::user();
        $isDonor = $user->id === $response->user_id;
        $isRecipient = $user->id === $response->bloodRequest->requested_by;
        $isAdmin = $user->role->value === 'admin' || $user->role === 'admin';
        
        if (!$isDonor && !$isRecipient && !$isAdmin) {
            abort(403, 'এই ডকুমেন্টটি দেখার অনুমতি আপনার নেই।');
        }

        if (!$response->proof_image_path) {
            abort(404, 'কোনো প্রমাণ সংযুক্ত নেই।');
        }

        if (!Storage::disk('private')->exists($response->proof_image_path)) {
            abort(404, 'ফাইলটি সার্ভারে পাওয়া যায়নি।');
        }

        return Storage::disk('private')->response($response->proof_image_path);
    }
}
