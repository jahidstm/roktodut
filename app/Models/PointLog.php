<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointLog extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'action_type',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    // ─── সম্ভাব্য action_type কনস্ট্যান্ট ────────────────────────────
    const ACTION_DONATION_COMPLETED       = 'donation_completed';
    const ACTION_FIRST_RESPONDER_BONUS    = 'first_responder_bonus';
    const ACTION_REFERRAL_SIGNUP          = 'referral_signup';
    const ACTION_REFERRAL_FIRST_DONATION  = 'referral_first_donation';
    const ACTION_RECIPIENT_REVIEW         = 'recipient_review';
    const ACTION_PROFILE_COMPLETION       = 'profile_completion';
    const ACTION_MANUAL_ADJUSTMENT        = 'manual_adjustment'; // অ্যাডমিন কর্তৃক

    // ─── বাংলা লেবেল ──────────────────────────────────────────────────
    public function actionLabel(): string
    {
        return match ($this->action_type) {
            self::ACTION_DONATION_COMPLETED      => '🩸 সফল রক্তদান',
            self::ACTION_FIRST_RESPONDER_BONUS   => '⚡ First Responder বোনাস',
            self::ACTION_REFERRAL_SIGNUP         => '👥 রেফারেল সাইন-আপ',
            self::ACTION_REFERRAL_FIRST_DONATION => '🎁 রেফারড ব্যক্তির প্রথম ডোনেশন',
            self::ACTION_RECIPIENT_REVIEW        => '💬 গ্রহীতার রিভিউ',
            self::ACTION_PROFILE_COMPLETION      => '✅ প্রোফাইল কমপ্লিট',
            self::ACTION_MANUAL_ADJUSTMENT       => '🔧 অ্যাডমিন অ্যাডজাস্টমেন্ট',
            default                              => $this->action_type,
        };
    }

    // ─── Relationships ─────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
