<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\PointLog;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * GamificationGovernanceController
 *
 * অ্যাডমিনের জন্য গ্যামিফিকেশন গভর্ন্যান্স কন্ট্রোল প্যানেল:
 *  — ইউজার লিস্ট (সার্চ + ফিল্টার)
 *  — Shadowban / Unshadowban (লিডারবোর্ড কন্ট্রোল)
 *  — Manual Point Adjustment (Bonus / Penalty)
 *  — Manual Badge Assignment
 *  — Point Log Audit Trail
 */
class GamificationGovernanceController extends Controller
{
    public function __construct(private readonly GamificationService $gamification) {}

    // ==========================================
    // ইউজার লিস্ট (Index)
    // ==========================================

    /**
     * ডোনারদের লিস্ট দেখাও — সার্চ + সাসপেক্ট ফিল্টার।
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'donor')
            ->with(['badges', 'district'])
            ->withCount('pointLogs');

        // সার্চ
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // শ্যাডোব্যান্ড ফিল্টার
        if ($request->boolean('banned_only')) {
            $query->where('is_shadowbanned', true);
        }

        $users = $query->orderByDesc('points')->paginate(20)->withQueryString();

        return view('admin.gamification.index', compact('users'));
    }

    // ==========================================
    // ইউজার ডিটেইলস (Show)
    // ==========================================

    /**
     * একটি নির্দিষ্ট ডোনারের সম্পূর্ণ গ্যামিফিকেশন প্রোফাইল।
     */
    public function show(User $user): View
    {
        $user->load(['badges', 'district', 'division']);

        // সর্বশেষ ১০টি পয়েন্ট লগ (অডিট ট্রেইল)
        $pointLogs = $user->pointLogs()
            ->latest()
            ->limit(10)
            ->get();

        // অ্যাসাইন করার জন্য সব ব্যাজ (ইউজারের যা নেই সেগুলো হাইলাইট)
        $allBadges     = Badge::orderBy('name')->get();
        $ownedBadgeIds = $user->badges->pluck('id')->toArray();

        return view('admin.gamification.show', compact(
            'user',
            'pointLogs',
            'allBadges',
            'ownedBadgeIds',
        ));
    }

    // ==========================================
    // Shadowban Toggle
    // ==========================================

    /**
     * ইউজারকে শ্যাডোব্যান / আনব্যান করো।
     */
    public function toggleShadowban(User $user): RedirectResponse
    {
        $newStatus = ! $user->is_shadowbanned;

        $user->update(['is_shadowbanned' => $newStatus]);

        Log::info('[GamificationGovernance] Shadowban toggled.', [
            'target_user_id' => $user->id,
            'admin_id'       => auth()->id(),
            'is_shadowbanned' => $newStatus,
        ]);

        $msg = $newStatus
            ? "✅ {$user->name}-কে লিডারবোর্ড থেকে শ্যাডোব্যান করা হয়েছে।"
            : "✅ {$user->name}-এর শ্যাডোব্যান তুলে নেওয়া হয়েছে।";

        return back()->with('success', $msg);
    }

    // ==========================================
    // Manual Point Adjustment
    // ==========================================

    /**
     * অ্যাডমিন কর্তৃক ম্যানুয়াল পয়েন্ট বোনাস বা ডিডাকশন।
     */
    public function adjustPoints(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'points' => ['required', 'integer', 'not_in:0', 'min:-10000', 'max:10000'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'points.not_in' => 'পয়েন্ট অবশ্যই শূন্য ছাড়া অন্য মান হতে হবে।',
            'reason.min'    => 'কারণ কমপক্ষে ৫ অক্ষরের হতে হবে।',
        ]);

        $points = (int) $data['points'];
        $reason = $data['reason'];

        // users.points আপডেট এবং point_logs-এ রেকর্ড তৈরি
        $this->gamification->awardPoints(
            user:       $user,
            points:     $points,
            actionType: PointLog::ACTION_MANUAL_ADJUSTMENT,
            metadata:   [
                'reason'   => $reason,
                'admin_id' => auth()->id(),
            ],
        );

        Log::info('[GamificationGovernance] Manual point adjustment.', [
            'target_user_id' => $user->id,
            'admin_id'       => auth()->id(),
            'points'         => $points,
            'reason'         => $reason,
        ]);

        $sign = $points > 0 ? '+' : '';
        return back()->with('success', "✅ {$user->name}-কে {$sign}{$points} পয়েন্ট অ্যাডজাস্ট করা হয়েছে।");
    }

    // ==========================================
    // Manual Badge Assignment
    // ==========================================

    /**
     * অ্যাডমিন কর্তৃক ম্যানুয়ালি ব্যাজ অ্যাসাইন বা রিমুভ।
     */
    public function assignBadge(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'badge_id' => ['required', 'exists:badges,id'],
            'action'   => ['required', 'in:attach,detach'],
        ]);

        $badge = Badge::findOrFail($data['badge_id']);

        if ($data['action'] === 'attach') {
            $alreadyHas = $user->badges()->where('badge_id', $badge->id)->exists();
            if (! $alreadyHas) {
                $user->badges()->attach($badge->id, [
                    'earned_at'  => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $msg = "✅ '{$badge->bn_name}' ব্যাজ {$user->name}-কে অ্যাসাইন করা হয়েছে।";
            } else {
                $msg = "ℹ️ {$user->name} ইতিমধ্যেই এই ব্যাজটির মালিক।";
            }
        } else {
            $user->badges()->detach($badge->id);
            $msg = "✅ '{$badge->bn_name}' ব্যাজ {$user->name}-এর কাছ থেকে সরিয়ে নেওয়া হয়েছে।";
        }

        Log::info('[GamificationGovernance] Badge assignment changed.', [
            'target_user_id' => $user->id,
            'admin_id'       => auth()->id(),
            'badge_id'       => $badge->id,
            'action'         => $data['action'],
        ]);

        return back()->with('success', $msg);
    }
}
