<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodRequestResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_request_id',
        'user_id',
        'status',
        'verification_pin',    // 🚀 নতুন কলাম
        'proof_image_path',   // 🚀 নতুন কলাম
        'verification_status', // 🚀 নতুন কলাম
        'donor_claimed_at',    // 🚀 নতুন কলাম
        'fulfilled_at',
        'fulfilled_by',
    ];

    protected $casts = [
        'donor_claimed_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    /**
     * মডেল বুট হওয়ার সময় ইভেন্ট লিসেনার সেট করা
     */
    protected static function booted()
    {
        static::creating(function ($response) {
            // ৪-ডিজিটের একটি ইউনিক পিন জেনারেট করা (যেমন: 0542)
            $response->verification_pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // ডিফল্ট স্ট্যাটাস সেট করা (যদি মাইগ্রেশনে না থাকে)
            if (empty($response->verification_status)) {
                $response->verification_status = 'pending';
            }
        });
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class);
    }

    /**
     * এটি আসলে ডোনার (Donor) ইউজার
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * কোড রিডাবিলিটির জন্য 'donor' রিলেশনও রাখা যেতে পারে
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
