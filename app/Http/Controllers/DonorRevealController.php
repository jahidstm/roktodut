<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Services\MathCaptchaService;
use App\Models\User;
use App\Models\BloodRequest;
use App\Models\PhoneRevealLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DonorRevealController extends Controller
{
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
    // Public Donor Directory Reveal Methods (Legacy/General)
    // ==========================================

    public function start(Request $request, User $donor, MathCaptchaService $mathCaptchaService)
    {
        if ($this->getRoleValue($donor->role) !== 'donor' && $this->getRoleValue($donor->role) !== UserRole::DONOR->value) {
            return $this->deny($request, (int)$donor->id, 'এই ইউজার ডোনার না।', 404);
        }

        // 🛡️ Privacy Guard: ডোনার ফোন নম্বর গোপন রেখেছেন
        if ($donor->hide_phone) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'       => false,
                    'hidden'   => true,
                    'donor_id' => $donor->id,
                    'message'  => '🛡️ এই ডোনার তাদের নম্বর গোপন রেখেছেন। তারা নিজে সিদ্ধান্ত নিয়ে আপনার সাথে যোগাযোগ করবেন।',
                ], 403);
            }
            return back()->with('error', '🛡️ এই ডোনার তাদের নম্বর গোপন রেখেছেন।');
        }

        $captchaQuestion = $mathCaptchaService->generate();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'donor_id' => $donor->id,
                'captchaQuestion' => $captchaQuestion,
                'expires_in_seconds' => 300,
            ]);
        }

        return back();
    }

    public function verify(Request $request, User $donor, MathCaptchaService $mathCaptchaService)
    {
        if ($this->getRoleValue($donor->role) !== 'donor' && $this->getRoleValue($donor->role) !== UserRole::DONOR->value) {
            return $this->deny($request, (int)$donor->id, 'এই ইউজার ডোনার না।', 404);
        }

        $request->validate([
            'captcha_answer' => ['required'],
        ], [
            'captcha_answer.required' => 'ক্যাপচা উত্তর দেওয়া বাধ্যতামূলক।',
        ]);

        $ipHash = hash('sha256', implode('|', [
            (string) $request->ip(),
            (string) $request->userAgent(),
            (string) $request->header('Accept-Language', ''),
            (string) config('app.key'),
        ]));

        $failedCaptchaBurst = DB::table('phone_reveal_attempts')
            ->where('ip_hash', $ipHash)
            ->where('status', 'failed_captcha')
            ->where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->count();

        if ($failedCaptchaBurst >= 5) {
            DB::table('phone_reveal_attempts')->insert([
                'donor_id' => $donor->id,
                'ip_hash' => $ipHash,
                'status' => 'rate_limited',
                'created_at' => now(),
            ]);

            return $this->deny($request, (int) $donor->id, 'বারবার ভুল যাচাইকরণ চেষ্টার কারণে এই ডিভাইসটি সাময়িকভাবে ব্লক করা হয়েছে। ৩০ মিনিট পরে আবার চেষ্টা করুন।', 429);
        }

        if (!$mathCaptchaService->verify($request->input('captcha_answer'))) {
            DB::table('phone_reveal_attempts')->insert([
                'donor_id' => $donor->id,
                'ip_hash' => $ipHash,
                'status' => 'failed_captcha',
                'created_at' => now(),
            ]);

            return $this->deny($request, (int)$donor->id, 'ক্যাপচা সঠিক নয় বা মেয়াদ শেষ হয়েছে। আবার চেষ্টা করুন।', 422);
        }

        $successThisHour = DB::table('phone_reveal_attempts')
            ->where('ip_hash', $ipHash)
            ->where('status', 'success')
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        $successForDonorToday = DB::table('phone_reveal_attempts')
            ->where('ip_hash', $ipHash)
            ->where('donor_id', $donor->id)
            ->where('status', 'success')
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->count();

        if ($successThisHour >= 3 || $successForDonorToday >= 1) {
            DB::table('phone_reveal_attempts')->insert([
                'donor_id' => $donor->id,
                'ip_hash' => $ipHash,
                'status' => 'rate_limited',
                'created_at' => now(),
            ]);

            return $this->deny($request, (int)$donor->id, 'অনেক বেশি অনুরোধ করা হয়েছে। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।', 429);
        }

        DB::table('phone_reveal_attempts')->insert([
            'donor_id' => $donor->id,
            'ip_hash' => $ipHash,
            'status' => 'success',
            'created_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'donor_id' => $donor->id,
                'phone' => (string) ($donor->phone ?? ''),
            ]);
        }

        return back();
    }

    // ==========================================
    // Blood Request Specific Secure Reveal Method
    // ==========================================

    public function revealPhone(Request $request, BloodRequest $bloodRequest, User $donor)
    {
        // ১. পলিসি এনফোর্সমেন্ট
        Gate::authorize('viewAcceptedDonors', $bloodRequest);

        // ২. ভ্যালিডেশন
        $hasAccepted = $bloodRequest->responses()
            ->where('user_id', $donor->id)
            ->where('status', 'accepted')
            ->exists();

        abort_unless($hasAccepted, 403, 'এই ডোনার এখনো রিকোয়েস্ট এক্সেপ্ট করেননি।');

        // ৩. স্মার্ট রেট লিমিটিং: ১৫ মিনিটে সর্বোচ্চ ৫ জন ইউনিক ডোনারের নাম্বার দেখতে পারবে
        $viewerId = $request->user()->id;

        $recentUniqueReveals = PhoneRevealLog::where('viewer_user_id', $viewerId)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->distinct('donor_id')
            ->count('donor_id');

        $hasSeenThisDonorRecently = PhoneRevealLog::where('viewer_user_id', $viewerId)
            ->where('donor_id', $donor->id)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->exists();

        if ($recentUniqueReveals >= 5 && !$hasSeenThisDonorRecently) {
            return response()->json([
                'success' => false,
                'message' => 'নিরাপত্তার স্বার্থে আপনি ১৫ মিনিটে সর্বোচ্চ ৫ জন ডোনারের নাম্বার দেখতে পারবেন। কিছুক্ষণ পর আবার চেষ্টা করুন।'
            ], 429);
        }

        // ৪. লগিং
        PhoneRevealLog::firstOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'viewer_user_id'   => $viewerId,
                'donor_id'         => $donor->id,
            ],
            [
                'revealed_at' => now(),
                'ip'          => $request->ip(),
                'user_agent'  => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        // ৫. রেসপন্স
        return response()->json([
            'success'       => true,
            'donor_user_id' => $donor->id,
            'phone'         => $donor->phone ?? 'নাম্বার দেওয়া নেই',
        ]);
    }
}
