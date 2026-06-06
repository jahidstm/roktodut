<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class DonorAvailability extends Model
{
    // ── Bitmask constants (Sun=0 in Carbon, but bit = 2^dayOfWeek) ──────────
    public const SUNDAY    = 1;   // 2^0
    public const MONDAY    = 2;   // 2^1
    public const TUESDAY   = 4;   // 2^2
    public const WEDNESDAY = 8;   // 2^3
    public const THURSDAY  = 16;  // 2^4
    public const FRIDAY    = 32;  // 2^5
    public const SATURDAY  = 64;  // 2^6
    public const ALL_DAYS  = 127; // 1+2+4+8+16+32+64

    // Bitmask labels (for display)
    public const DAY_LABELS = [
        self::SUNDAY    => 'রবিবার',
        self::MONDAY    => 'সোমবার',
        self::TUESDAY   => 'মঙ্গলবার',
        self::WEDNESDAY => 'বুধবার',
        self::THURSDAY  => 'বৃহস্পতিবার',
        self::FRIDAY    => 'শুক্রবার',
        self::SATURDAY  => 'শনিবার',
    ];

    // Ordered bit values for UI iteration
    public const DAY_BITS = [
        self::SUNDAY, self::MONDAY, self::TUESDAY, self::WEDNESDAY,
        self::THURSDAY, self::FRIDAY, self::SATURDAY,
    ];

    protected $fillable = [
        'user_id',
        'type',
        'weekdays_bitmask',
        'specific_date',
        'date_from',
        'date_to',
        'time_from',
        'time_to',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'specific_date'    => 'date',
            'date_from'        => 'date',
            'date_to'          => 'date',
            'is_active'        => 'boolean',
            'weekdays_bitmask' => 'integer',
        ];
    }

    // ── Relationship ─────────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper: Convert bitmask → human-readable day list ───────────────────
    public function getActiveDaysAttribute(): array
    {
        if ($this->weekdays_bitmask === null) {
            return [];
        }
        $days = [];
        foreach (self::DAY_BITS as $bit) {
            if ($this->weekdays_bitmask & $bit) {
                $days[] = self::DAY_LABELS[$bit];
            }
        }
        return $days;
    }

    // ── Helper: Is this rule covering a given Carbon datetime? ───────────────
    public function isAvailableAt(Carbon $dt): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $timeNow   = $dt->format('H:i:s');
        $inWindow  = $this->time_from === null
            || ($timeNow >= $this->time_from && $timeNow <= $this->time_to);

        return match ($this->type) {
            'weekly' => $this->weekdays_bitmask !== null
                && ($this->weekdays_bitmask & (1 << $dt->dayOfWeek)) > 0
                && $inWindow,

            'specific_date' => $this->specific_date !== null
                && $this->specific_date->isSameDay($dt)
                && $inWindow,

            'date_range' => $this->date_from !== null
                && $this->date_to !== null
                && $dt->greaterThanOrEqualTo($this->date_from->startOfDay())
                && $dt->lessThanOrEqualTo($this->date_to->endOfDay())
                && $inWindow,

            default => false,
        };
    }

    // ── Static helper: day bit from Carbon dayOfWeek (0=Sun … 6=Sat) ────────
    public static function bitForDay(int $dayOfWeek): int
    {
        return 1 << $dayOfWeek;
    }
}
