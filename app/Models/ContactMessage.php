<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContactMessage — যোগাযোগ ফর্মের সকল বার্তা সংরক্ষণ করে।
 *
 * @property int         $id
 * @property int|null    $user_id
 * @property string|null $name
 * @property string      $email
 * @property string|null $phone
 * @property string      $subject
 * @property string      $message
 * @property string      $status         new | in_progress | resolved | spam
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ==================== Relationships ====================

    /**
     * লগইন করা ইউজার হলে তার সম্পর্ক।
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ==================== Scopes ====================

    /**
     * শুধু নতুন (unread) বার্তা ফিল্টার।
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * স্প্যাম বাদে সকল বার্তা।
     */
    public function scopeNotSpam($query)
    {
        return $query->where('status', '!=', 'spam');
    }

    // ==================== Accessors ====================

    /**
     * বাংলায় স্ট্যাটাস লেবেল।
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new'         => 'নতুন',
            'in_progress' => 'প্রক্রিয়াধীন',
            'resolved'    => 'সমাধান হয়েছে',
            'spam'        => 'স্প্যাম',
            default       => 'অজানা',
        };
    }

    /**
     * বার্তার সংক্ষিপ্ত প্রিভিউ (প্রথম ১০০ অক্ষর)।
     */
    public function getPreviewAttribute(): string
    {
        return mb_substr($this->message, 0, 100) . (mb_strlen($this->message) > 100 ? '...' : '');
    }

    /**
     * প্রেরকের প্রদর্শন নাম (লগইন ইউজার হলে তার নাম, না হলে ফর্মের নাম)।
     */
    public function getSenderNameAttribute(): string
    {
        return $this->user?->name ?? $this->name ?? 'অজ্ঞাত';
    }

    /**
     * স্ট্যাটাস ব্যাজের রঙ (Tailwind class)।
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new'         => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-amber-100 text-amber-700',
            'resolved'    => 'bg-emerald-100 text-emerald-700',
            'spam'        => 'bg-red-100 text-red-700',
            default       => 'bg-slate-100 text-slate-700',
        };
    }
}
