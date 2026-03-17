<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\BloodRequest;          // 👈 নতুন যুক্ত করা হয়েছে
use App\Models\PhoneRevealLog;        // 👈 নতুন যুক্ত করা হয়েছে
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;  // 👈 নতুন যুক্ত করা হয়েছে

class DonorRevealController extends Controller
{
    private function challengeKey(int $donorId): string
    {
        return "reveal_challenge.$donorId";
    }

    private function revealedKey(int $donorId): string
    {
        return "revealed_phone.$donorId";
    }

    private function deny(Request $request, int $donorId, string $message, int $status = 403)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'message' => $message,
                'donor_id' => $donorId,
            ], $status);
        }

        return back()
            ->with('error', $message)
            ->with('reveal_target', $donorId);
    }

    private function getRoleValue($role)
    {
        return $role instanceof UserRole ? $role->value : (string) ($role ?? '');
    }

    // ==========================================
    // Public Donor Directory Reveal Methods
    // ==========================================

    public function start(Request $request, User $donor)
    {
        if ($this->getRoleValue($donor->role) !== 'donor' && $this->getRoleValue($donor->role) !== UserRole::DONOR->value) {
            return $this->deny($request, (int)$donor->id, 'এই ইউজার ডোনার না।', 404);
        }

        $a = random_int(2, 9);
        $b = random_int(2, 9);

        $request->session()->put($this->challengeKey($donor->id), [
            'question' => "$a + $b = ?",
            'answer' => $a + $b,
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        // === THE ARCHITECTURAL FIX ===
        // AJAX রিকোয়েস্টের ক্ষেত্রেও পেজ রিলোড হওয়ার পর Blade-কে জানাতে হবে কার চ্যালেঞ্জ ফর্ম দেখাতে হবে
        $request->session()->flash('reveal_target', $donor->id);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'donor_id' => $donor->id,
                'challenge_set' => true,
                'expires_in_seconds' => 300,
            ]);
        }

        return back()->with('reveal_target', $donor->id);
    }

    public function verify(Request $request, User $donor)
    {
        if ($this->getRoleValue($donor->role) !== 'donor' && $this->getRoleValue($donor->role) !== UserRole::DONOR->value) {
            return $this->deny($request, (int)$donor->id, 'এই ইউজার ডোনার না।', 404);
        }

        $payload = $request->session()->get($this->challengeKey($donor->id));
        if (!$payload || now()->timestamp > (int)($payload['expires_at'] ?? 0)) {
            return $this->deny($request, (int)$donor->id, 'চ্যালেঞ্জের সময় শেষ। আবার চেষ্টা করুন।', 422);
        }

        $request->validate([
            'answer' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        if ((int)$request->input('answer') !== (int)$payload['answer']) {
            return $this->deny($request, (int)$donor->id, 'ভুল উত্তর। আবার চেষ্টা করুন।', 422);
        }

        // Rate limit: max 5 successful reveals / 15 minutes / IP
        $ip = $request->ip() ?? 'unknown';
        $windowStart = Carbon::now()->subMinutes(15);

        $recentSuccessCount = DB::table('phone_reveal_logs')
            ->where('ip', $ip)
            ->where('created_at', '>=', $windowStart)
            ->count();

        if ($recentSuccessCount >= 5) {
            return $this->deny($request, (int)$donor->id, 'আপনি অনেকবার ফোন দেখার চেষ্টা করেছেন। ১৫ মিনিট পরে আবার চেষ্টা করুন।', 429);
        }

        DB::table('phone_reveal_logs')->insert([
            'donor_id' => $donor->id,
            'ip' => $ip,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request->session()->put($this->revealedKey($donor->id), (string)$donor->phone);
        $request->session()->forget($this->challengeKey($donor->id));

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'donor_id' => $donor->id,
                'revealed' => true,
            ]);
        }

        return back()->with('reveal_target', $donor->id);
    }

    // ==========================================
    // Blood Request Specific Reveal Method
    // ==========================================

    /**
     * রিকোয়েস্টের মালিক কর্তৃক ডোনারের ফোন নাম্বার রিভিল করার লজিক
     */
    public function revealPhone(Request $request, BloodRequest $bloodRequest, User $donor)
    {
        // ১. পলিসি এনফোর্সমেন্ট: শুধুমাত্র মালিক বা অ্যাডমিন দেখতে পারবে
        Gate::authorize('viewAcceptedDonors', $bloodRequest);

        // ২. ভ্যালিডেশন: ডোনার কি আসলেই এই রিকোয়েস্টটি এক্সেপ্ট করেছে?
        $hasAccepted = $bloodRequest->responses()
            ->where('user_id', $donor->id)
            ->where('status', 'accepted')
            ->exists();

        abort_unless($hasAccepted, 403, 'এই ডোনার এখনো রিকোয়েস্ট এক্সেপ্ট করেননি।');

        // ৩. লগিং: কে কখন কার নাম্বার দেখল তার রেকর্ড রাখা
        PhoneRevealLog::firstOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'viewer_user_id'   => $request->user()->id,
                'donor_user_id'    => $donor->id,
            ],
            [
                'revealed_at' => now(),
                'ip'          => $request->ip(),
                'user_agent'  => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        // ৪. রেসপন্স: জেসন ডেটা রিটার্ন করা (ফ্রন্টএন্ডের জন্য success ফ্লাগ সহ)
        return response()->json([
            'success'       => true,
            'donor_user_id' => $donor->id,
            'phone'         => $donor->phone ?? 'নাম্বার দেওয়া নেই',
        ]);
    }
}