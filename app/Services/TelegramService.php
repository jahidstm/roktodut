<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $token;
    private string $baseUrl;

    public function __construct()
    {
        $this->token   = config('services.telegram.token', '');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    // ─────────────────────────────────────────────────────────────
    // Core: যেকোনো chat_id-তে মেসেজ পাঠানো
    // ─────────────────────────────────────────────────────────────
    public function send(string|int $chatId, string $text, bool $html = true): bool
    {
        if (empty($this->token)) {
            Log::warning('[Telegram] Bot token not configured.');
            return false;
        }

        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => $html ? 'HTML' : 'Markdown',
                'disable_web_page_preview' => true,
            ]);

            if (!$response->successful()) {
                Log::error('[Telegram] Send failed', [
                    'chat_id' => $chatId,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('[Telegram] Exception: ' . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Webhook সেট করা (একবার চালালেই হয়)
    // ─────────────────────────────────────────────────────────────
    public function setWebhook(string $url): array
    {
        $response = Http::post("{$this->baseUrl}/setWebhook", [
            'url'             => $url,
            'allowed_updates' => ['message'],
            'drop_pending_updates' => true,
        ]);

        return $response->json();
    }

    // ─────────────────────────────────────────────────────────────
    // Webhook delete করা (ডেভেলপমেন্টে কাজে লাগে)
    // ─────────────────────────────────────────────────────────────
    public function deleteWebhook(): array
    {
        $response = Http::post("{$this->baseUrl}/deleteWebhook");
        return $response->json();
    }

    // ─────────────────────────────────────────────────────────────
    // 🩸 ইমার্জেন্সি ব্লাড রিকোয়েস্ট অ্যালার্ট টেমপ্লেট
    // ─────────────────────────────────────────────────────────────
    public function sendBloodAlert(string|int $chatId, array $data): bool
    {
        $urgencyLabel = match($data['urgency'] ?? 'normal') {
            'emergency' => '🚨 জরুরি (Emergency)',
            'urgent'    => '⚠️ আর্জেন্ট (Urgent)',
            default     => '🩸 সাধারণ (Normal)',
        };

        $text = <<<MSG
🔴 <b>নতুন রক্তের অনুরোধ! {$urgencyLabel}</b>

🩸 <b>রক্তের গ্রুপ:</b> {$data['blood_group']}
🏥 <b>হাসপাতাল:</b> {$data['hospital']}
📍 <b>লোকেশন:</b> {$data['location']}
📦 <b>ব্যাগ প্রয়োজন:</b> {$data['bags_needed']}
⏰ <b>কখন লাগবে:</b> {$data['needed_at']}

👉 <a href="{$data['request_url']}">এখানে ক্লিক করে রেসপন্ড করুন</a>

<i>আপনি রক্তদূতের ডোনার হিসেবে নিবন্ধিত বলে এই অ্যালার্ট পেয়েছেন।</i>
MSG;

        return $this->send($chatId, $text);
    }

    // ─────────────────────────────────────────────────────────────
    // ✅ Connection সফল হওয়ার ওয়েলকাম মেসেজ
    // ─────────────────────────────────────────────────────────────
    public function sendWelcomeMessage(string|int $chatId, string $donorName): bool
    {
        $text = <<<MSG
✅ <b>সংযোগ সফল হয়েছে!</b>

স্বাগতম, <b>{$donorName}</b>! 🎉

আপনার টেলিগ্রাম অ্যাকাউন্ট <b>রক্তদূত</b> প্ল্যাটফর্মের সাথে সংযুক্ত হয়েছে।

এখন থেকে আপনার কাছের কোনো মুমূর্ষু রোগীর রক্তের প্রয়োজন হলে আপনি <b>সবার আগে</b> এই চ্যানেলে অ্যালার্ট পাবেন।

রক্তদান করুন, জীবন বাঁচান। ❤️
MSG;

        return $this->send($chatId, $text);
    }
    // ─────────────────────────────────────────────────────────────
    // 🚨 ওয়ান-ওয়ে হ্যান্ডশেক: ডোনারের নম্বর রোগীর Telegram-এ পুশ করা
    // ─────────────────────────────────────────────────────────────
    public function sendDonorPingToRequester(
        string|int $requesterChatId,
        string $donorName,
        string $donorBloodGroup,
        string $donorPhone,
        string $requestUrl
    ): bool {
        $text = <<<MSG
🚨 <b>জরুরি: একজন রক্তদাতা আপনার রিকোয়েস্টে সাড়া দিয়েছেন!</b>

👤 <b>দাতা:</b> {$donorName}
🩸 <b>গ্রুপ:</b> {$donorBloodGroup}

📞 <b>এখনই কল দিন:</b> <code>{$donorPhone}</code>

<i>এই নম্বরে কল করুন এবং দাতাকে আসার ব্যবস্থা করুন। দাতার সাথে হাসপাতালের ঠিকানা শেয়ার করুন।</i>

👉 <a href="{$requestUrl}">রিকোয়েস্ট ডিটেইলস দেখুন</a>

<i>— রক্তদূত প্ল্যাটফর্ম</i>
MSG;

        return $this->send($requesterChatId, $text);
    }
}
