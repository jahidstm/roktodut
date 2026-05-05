<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\Request;

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * PublicVerificationController — Dynamic QR Smart Card পাবলিক ভেরিফিকেশন
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * এই কন্ট্রোলার সম্পূর্ণ PUBLIC। কোনো auth বা login middleware নেই।
 * উদ্দেশ্য: QR কোড স্ক্যান করে যে কেউ ডোনারের যাচাই করা পরিচয় দেখতে পারবে।
 *
 * Security Architecture:
 *  ✅ শুধুমাত্র whitelisted ফিল্ড view-তে পাঠানো হয়
 *  🚫 ফোন নম্বর — কখনোই view-তে যাবে না
 *  🚫 ইমেইল    — কখনোই view-তে যাবে না
 *  🚫 NID নম্বর — কখনোই view-তে যাবে না
 *  🚫 address   — কখনোই view-তে যাবে না
 *  ✅ throttle:60,1 — brute-force ও scraping প্রতিরোধ (route-এ সেট করা)
 *  ✅ shadow-ban চেক — ব্যানড ইউজার = "Not Found"
 *  ✅ NID verified চেক — verify না হলে QR invalid
 * ─────────────────────────────────────────────────────────────────────────────
 */
class PublicVerificationController extends Controller
{
    public function __construct(
        private readonly GamificationService $gamification,
    ) {}

    /**
     * QR token দিয়ে ডোনারের পাবলিক Smart Card দেখাও।
     *
     * Route: GET /verify/{token}  (throttle:60,1 — auth নেই)
     *
     * @param  string $token  — URL-এর QR token (64-char hex)
     */
    public function show(string $token): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        // ─── ১. Token Lookup ─────────────────────────────────────────────
        //
        // nid_status চেক intentionally DB-তে করা হচ্ছে যাতে
        // token সঠিক হলেও verify না হলে invalid দেখায়।
        $user = User::where('qr_token', $token)
            ->where('nid_status', 'verified')
            ->first();

        // ─── ২. Shadow-Ban / Not Found চেক ──────────────────────────────
        //
        // Shadow-banned বা token না পেলে: 404-এর মতো দেখায় কিন্তু
        // কারণ জানানো হয় না (Enumeration Attack প্রতিরোধ)।
        if (! $user || $user->is_shadowbanned) {
            return view('public.verification.invalid', [
                'message' => 'এই QR কোডটি বৈধ নয় বা মেয়াদোত্তীর্ণ।',
            ]);
        }

        // ─── ৩. Availability — Coarse Check ─────────────────────────────
        //
        // নিয়ম: is_available এবং 120-দিনের জৈবিক কুলডাউন উভয়ই পাস করতে হবে।
        // সতর্কতা: শুধু "Available" বা "In Cooldown" — কোনো তারিখ নয়।
        $isDonatable  = $user->canDonate();   // 120-দিনের কুলডাউন চেক (User model)
        $isAvailable  = $user->is_available && $isDonatable;

        $availability = $isAvailable ? 'available' : 'cooldown';

        // ─── ৪. Verified Badges (whitelisted slugs only) ─────────────────
        //
        // Pivot table থেকে badge list নেওয়া। প্রতিটি badge-এর জন্য
        // GamificationService থেকে display data (label, emoji, color) নেওয়া।
        $badges = $user->badges()
            ->get()
            ->map(fn($badge) => GamificationService::getBadgeDisplayData($badge->name));

        // ─── ৫. Blood Group ──────────────────────────────────────────────
        // Enum থেকে string value বের করো (e.g., "A+", "O-")
        $bloodGroup = $user->blood_group?->value ?? 'অজানা';

        // ─── ৬. View-তে শুধুমাত্র Safe Data পাঠাও ───────────────────────
        //
        // ⚠️  CRITICAL SECURITY RULE:
        //     $user object সরাসরি view-তে পাস করা নিষিদ্ধ।
        //     কারণ: Blade-এ অসাবধানতাবশত {{ $user->phone }} টাইপ করলে
        //     data leak হবে। শুধুমাত্র whitelisted scalar/array পাস করা হচ্ছে।
        return view('public.verification.show', [
            'user_id'      => $user->id,
            'name'         => $user->name,
            'blood_group'  => $bloodGroup,
            'badges'       => $badges,
            'availability' => $availability,   // 'available' | 'cooldown'
            'district'     => $user->district?->name ?? null,
            'verified_at'  => $user->updated_at?->format('d M Y'), // approx
            'qr_token'     => $user->qr_token,
            // ─── নিম্নলিখিত কিছুই পাঠানো যাবে না ───
            // ❌  'phone'   => ...
            // ❌  'email'   => ...
            // ❌  'nid_number' => ...
            // ❌  'address' => ...
        ]);
    }
}
