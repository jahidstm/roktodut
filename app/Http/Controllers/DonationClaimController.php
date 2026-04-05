<?php

namespace App\Http\Controllers;

use App\Models\BloodRequestResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DonationClaimController extends Controller
{
    /**
     * ডোনারের ডোনেশন ক্লেইম হ্যান্ডেল করা (PIN or Image)
     */
    public function store(Request $request, BloodRequestResponse $response)
    {
        // 🛡️ সিকিউরিটি চেক: শুধুমাত্র আসল ডোনারই ক্লেইম করতে পারবে
        if ($response->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // ভ্যালিডেশন
        $request->validate([
            'claim_method' => 'required|in:pin,image',
            'pin' => 'required_if:claim_method,pin|nullable|string|size:4',
            'proof_image' => 'required_if:claim_method,image|nullable|image|max:2048',
        ]);

        // ১. পিন মেথড লজিক
        if ($request->claim_method === 'pin') {
            if ($request->pin === $response->verification_pin) {
                $response->update([
                    'verification_status' => 'verified',
                    'donor_claimed_at' => now(),
                ]);

                // 🏅 রিওয়ার্ড পয়েন্ট প্রদান
                $donor = Auth::user();
                $donor->increment('reward_points', 50);
                $donor->increment('total_verified_donations');

                return back()->with('success', 'পিন মিলেছে! আপনার রক্তদান সফলভাবে ভেরিফাইড হয়েছে এবং আপনি ৫০ পয়েন্ট পেয়েছেন।');
            }
            return back()->with('error', 'দুঃখিত, পিনটি সঠিক নয়। আবার চেষ্টা করুন।');
        }

        // ২. ইমেজ মেথড লজিক
        if ($request->claim_method === 'image') {
            $path = $request->file('proof_image')->store('donation_proofs', 'public');

            $response->update([
                'proof_image_path' => $path,
                'verification_status' => 'claimed', // রিভিউ এর জন্য 'claimed' স্ট্যাটাস
                'donor_claimed_at' => now(),
            ]);

            return back()->with('success', 'আপনার প্রমাণটি সফলভাবে জমা হয়েছে। অ্যাডমিন বা গ্রহীতা যাচাই করার পর পয়েন্ট যুক্ত হবে।');
        }
    }

    /**
     * রোগীর লোকের (Recipient) পক্ষ থেকে ডোনেশন কনফার্ম বা ডিসপুট করা
     */
    public function verifyByRecipient(Request $request, BloodRequestResponse $response)
    {
        // 🛡️ সিকিউরিটি চেক: রিকোয়েস্টটি কি এই ইউজারেরই কি না (requested_by কলাম চেক)
        if ($response->bloodRequest->requested_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'decision' => 'required|in:approve,dispute'
        ]);

        if ($request->decision === 'approve') {
            $response->update(['verification_status' => 'verified']);

            // ডোনারকে রিওয়ার্ড পয়েন্ট দেওয়া
            $donor = $response->user;
            if ($donor) {
                $donor->increment('reward_points', 50);
                $donor->increment('total_verified_donations');
            }

            return back()->with('success', 'ধন্যবাদ! আপনার কনফার্মেশনের ফলে ডোনারকে ৫০ পয়েন্ট দেওয়া হয়েছে।');
        }

        if ($request->decision === 'dispute') {
            $response->update(['verification_status' => 'disputed']);
            return back()->with('error', 'আপনার অভিযোগটি গ্রহণ করা হয়েছে। অ্যাডমিন এটি খতিয়ে দেখবে।');
        }
    }

    /**
     * 🛡️ অ্যাডমিনের ম্যানুয়াল ভেরিফিকেশন (প্রুফ ছবি দেখে)
     */
    public function adminVerify(Request $request, BloodRequestResponse $response)
    {
        // সিকিউরিটি চেক: শুধুমাত্র অ্যাডমিন এটি করতে পারবে
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:verified,rejected'
        ]);

        if ($request->status === 'verified') {
            $response->update(['verification_status' => 'verified']);

            // ডোনারকে রিওয়ার্ড পয়েন্ট দেওয়া
            $donor = $response->user;
            if ($donor) {
                $donor->increment('reward_points', 50);
                $donor->increment('total_verified_donations');
            }

            return back()->with('success', 'প্রমাণ যাচাই করে ডোনেশনটি সফলভাবে অ্যাপ্রুভ করা হয়েছে।');
        }

        if ($request->status === 'rejected') {
            $response->update(['verification_status' => 'rejected']);
            return back()->with('error', 'প্রমাণ সঠিক না হওয়ায় ডোনেশন বাতিল করা হয়েছে।');
        }
    }
}
