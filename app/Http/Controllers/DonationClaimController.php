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
                    'verification_status' => 'verified',
                    'donor_claimed_at'    => now(),
                ]);

                $donor = Auth::user();
                if ($donor) {
                    $donor->increment('total_verified_donations');

                    // 🎯 First Responder বোনাস চেক
                    $bloodRequest  = $response->bloodRequest;
                    $isFirstResponder = false;
                    if ($bloodRequest && $bloodRequest->urgency === 'emergency') {
                        $responseTimeHours = $bloodRequest->created_at->diffInHours($response->created_at);
                        if ($responseTimeHours <= 3) {
                            $isFirstResponder = true;
                        }
                    }

                    // 🏆 GamificationService দিয়ে পয়েন্ট ও ব্যাজ আপডেট
                    $this->gamification->awardDonationPoints($donor, $isFirstResponder);
                }

                $msg = '🎉 পিন মিলেছে! আপনার রক্তদান সফলভাবে ভেরিফাইড হয়েছে। +৫০ পয়েন্ট অর্জিত হয়েছে!';
                if ($isFirstResponder ?? false) {
                    $msg .= ' এবং First Responder বোনাস হিসেবে আরও +১০ পয়েন্ট পেয়েছেন!';
                }

                return back()->with('success', $msg);
            }
            return back()->with('error', 'দুঃখিত, পিনটি সঠিক নয়।');
        }

        if ($request->claim_method === 'image') {
            $path = $request->file('proof_image')->store('donation_proofs', 'public');
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
            $response->update(['verification_status' => 'verified']);

            $donor = $response->user;
            if ($donor) {
                $donor->increment('total_verified_donations');
                $this->gamification->awardDonationPoints($donor);

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
            $response->update(['verification_status' => 'verified']);

            $donor = $response->user;
            if ($donor) {
                $donor->increment('total_verified_donations');
                $this->gamification->awardDonationPoints($donor);
            }

            return back()->with('success', '✅ অ্যাপ্রুভ করা হয়েছে। ডোনারকে পয়েন্ট ও ব্যাজ দেওয়া হয়েছে।');
        }

        if ($request->status === 'rejected') {
            $response->update(['verification_status' => 'rejected']);
            return back()->with('error', 'বাতিল করা হয়েছে।');
        }
    }
}
