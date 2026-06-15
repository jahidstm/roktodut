<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

/**
 * CooldownReminderNotification
 *
 * Donor-এর donation cooldown শেষ হওয়ার ৭/৩/০ দিন আগে পাঠানো হয়।
 *
 * Channels:
 *  - database  : in-app bell notification
 *  - fcm       : mobile push (Firebase)
 *  - telegram  : Telegram bot message
 */
class CooldownReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param int    $daysLeft   7, 3, or 0 — কতদিন বাকি
     * @param string $eligibleDate  পরবর্তী eligible তারিখ (formatted)
     */
    public function __construct(
        private readonly int    $daysLeft,
        private readonly string $eligibleDate,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    public function via(object $notifiable): array
    {
        return ['database'];   // FCM ও Telegram handle() এ আলাদা পাঠানো হবে
    }

    // ─────────────────────────────────────────────────────────────────────────
    // In-app (database) payload
    // ─────────────────────────────────────────────────────────────────────────
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'cooldown_reminder',
            'days_left'      => $this->daysLeft,
            'eligible_date'  => $this->eligibleDate,
            'title'          => $this->getTitle(),
            'message'        => $this->getMessage(),
            'url'            => route('donor.dashboard'),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FCM push (called manually from command, not via Laravel's notification)
    // ─────────────────────────────────────────────────────────────────────────
    public function sendFcm(User $user): void
    {
        $token = $user->fcm_token;
        if (empty($token)) return;

        try {
            $messaging = app('firebase.messaging');
            $message   = CloudMessage::new()
                ->withNotification(FcmNotification::create(
                    $this->getTitle(),
                    $this->getMessage(),
                ))
                ->withData([
                    'type'     => 'cooldown_reminder',
                    'days_left' => (string) $this->daysLeft,
                    'url'      => route('donor.dashboard'),
                ]);

            $report = $messaging->sendMulticast($message, [$token]);

            // Invalid token cleanup
            $bad = array_merge($report->unknownTokens(), $report->invalidTokens());
            if (!empty($bad)) {
                User::where('fcm_token', $token)->update(['fcm_token' => null]);
            }
        } catch (\Throwable $e) {
            Log::warning('[CooldownReminder] FCM send failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Telegram message (called manually from command)
    // ─────────────────────────────────────────────────────────────────────────
    public function sendTelegram(User $user): void
    {
        $chatId = $user->telegram_chat_id;
        if (empty($chatId)) return;

        try {
            $emoji   = $this->daysLeft === 0 ? '🩸' : ($this->daysLeft === 3 ? '⏰' : '🎉');
            $dashUrl = route('donor.dashboard');

            $text = <<<MSG
            {$emoji} <b>{$this->getTitle()}</b>

            {$this->getMessage()}

            📅 <b>পরবর্তী donation তারিখ:</b> {$this->eligibleDate}

            👉 <a href="{$dashUrl}">ডোনার ড্যাশবোর্ড দেখুন</a>

            <i>— রক্তদূত প্ল্যাটফর্ম</i>
            MSG;

            /** @var \App\Services\TelegramService $telegram */
            $telegram = app(\App\Services\TelegramService::class);
            $telegram->send($chatId, trim($text));
        } catch (\Throwable $e) {
            Log::warning('[CooldownReminder] Telegram send failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Message copy — দিন অনুযায়ী আলাদা
    // ─────────────────────────────────────────────────────────────────────────
    public function getTitle(): string
    {
        return match ($this->daysLeft) {
            0       => 'আজই রক্ত দেওয়ার দিন!',
            3       => 'মাত্র ৩ দিন বাকি — প্রস্তুত থাকুন!',
            default => 'আর মাত্র ৭ দিন!',
        };
    }

    public function getMessage(): string
    {
        return match ($this->daysLeft) {
            0 => "আপনার cooldown শেষ হয়েছে! আজ ({$this->eligibleDate}) থেকেই আবার রক্ত দিতে পারবেন। আপনার একটি donation কারো জীবন বাঁচাতে পারে।",
            3 => "আর মাত্র ৩ দিন পরেই ({$this->eligibleDate}) আপনি রক্ত দেওয়ার জন্য যোগ্য হবেন। প্রস্তুতি নিন এবং কাছের donation center-এ যান।",
            default => "আর মাত্র ৭ দিন পরেই ({$this->eligibleDate}) আপনি আবার রক্তদান করতে পারবেন! আপনার মতো donors-এর কারণেই এই প্ল্যাটফর্ম কাজ করে।",
        };
    }
}
