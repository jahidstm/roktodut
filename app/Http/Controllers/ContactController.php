<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

/**
 * ContactController
 *
 * যোগাযোগ ফর্ম দেখানো ও সাবমিশন প্রসেস করে।
 *
 * Throttle: web.php রুটে বসানো হয়েছে
 *   - Guest  : throttle:contact-guest  → ২ req/min
 *   - Auth   : throttle:contact-auth   → ৫ req/min
 *
 * Honeypot : 'website' ফিল্ড ভরা থাকলে silently spam হিসেবে ট্র্যাক হয়।
 */
class ContactController extends Controller
{
    // ─── Public: ফর্ম দেখানো ─────────────────────────────────────────────────

    public function create(): View
    {
        return view('contact.create');
    }

    // ─── Public: ফর্ম সাবমিট করা ─────────────────────────────────────────────

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        // ─── ১. Honeypot Detection ───────────────────────────────────────────
        // 'website' ফিল্ড লুকানো থাকে — বট ভরে দেয়, মানুষ দেয় না।
        if (filled($request->input('website'))) {
            // Silently spam হিসেবে save করো, ব্যবহারকারীকে কিছু বলো না।
            $this->persistMessage($request, status: 'spam');

            // Success response দিয়ে বট-কে বিভ্রান্ত করো
            return redirect()->back()
                ->with('success', 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে! আমরা শীঘ্রই যোগাযোগ করব।');
        }

        // ─── ২. বার্তা ডাটাবেসে সেভ করো ────────────────────────────────────
        $contactMessage = $this->persistMessage($request, status: 'new');

        // ─── ৩. সকল Admin ইউজারকে নোটিফাই করো ──────────────────────────────
        $admins = User::where('role', 'admin')->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewContactMessageNotification($contactMessage));
        }

        // ─── ৪. সফল রেসপন্স ─────────────────────────────────────────────────
        return redirect()->route('contact.create')
            ->with('success', 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে! আমরা শীঘ্রই যোগাযোগ করব।');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * বার্তা ডাটাবেসে সেভ করে।
     * - IP Address + User-Agent ক্যাপচার করে নিরাপত্তার জন্য।
     * - লগইন ইউজার হলে name/email তাঁর প্রোফাইল থেকে নেওয়া হয়।
     */
    private function persistMessage(StoreContactMessageRequest $request, string $status): ContactMessage
    {
        $user = $request->user();

        return ContactMessage::create([
            'user_id'    => $user?->id,
            'name'       => $user?->name ?? $request->validated('name'),
            'email'      => $user?->email ?? $request->validated('email'),
            'phone'      => $request->validated('phone'),
            'subject'    => $request->validated('subject'),
            'message'    => $request->validated('message'),
            'status'     => $status,
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr($request->userAgent() ?? '', 0, 500),
        ]);
    }
}
