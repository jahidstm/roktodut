<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    public function __construct(private readonly TelegramService $telegram) {}

    // ─────────────────────────────────────────────────────────────
    // ১. ডোনার "Connect Telegram" ক্লিক করলে এই মেথড চলবে
    //    → একটি ইউনিক verify token তৈরি করে ডোনারকে বটের দিকে রিডাইরেক্ট করবে
    // ─────────────────────────────────────────────────────────────
    public function generateConnectLink(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        // ইউনিক 24-char token তৈরি করা
        $token = Str::random(24);

        $user->update(['telegram_verify_token' => $token]);

        $botUsername = config('services.telegram.username');
        $deeplink    = "https://t.me/{$botUsername}?start=verify_{$token}";

        return redirect()->away($deeplink);
    }

    // ─────────────────────────────────────────────────────────────
    // ২. Telegram Webhook — বট মেসেজ গ্রহণ করবে
    //    POST /telegram/webhook
    // ─────────────────────────────────────────────────────────────
    public function webhook(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! $this->hasValidWebhookSecret($request)) {
            Log::warning('[TelegramWebhook] Invalid webhook secret token.', [
                'ip' => $request->ip(),
                'ua' => (string) $request->userAgent(),
            ]);

            abort(403, 'Invalid webhook secret.');
        }

        $update = $request->all();

        // শুধুমাত্র /start verify_TOKEN কমান্ড প্রসেস করব
        $messageText = $update['message']['text'] ?? '';
        $chatId      = $update['message']['chat']['id'] ?? null;
        $firstName   = $update['message']['from']['first_name'] ?? 'বন্ধু';

        if (!$chatId) {
            return response()->json(['ok' => true]);
        }

        // /start verify_XXXXX কমান্ড চেক করা
        if (str_starts_with($messageText, '/start verify_')) {
            $token = str_replace('/start verify_', '', $messageText);
            $this->handleVerification($chatId, $token);
            return response()->json(['ok' => true]);
        }

        // /start শুধু এলে ওয়েলকাম মেসেজ
        if ($messageText === '/start') {
            $this->telegram->send($chatId, "🩸 <b>রক্তদূত বটে স্বাগতম!</b>\n\nআপনার অ্যাকাউন্ট কানেক্ট করতে রক্তদূত প্রোফাইল পেজ থেকে <b>\"Connect Telegram\"</b> বাটনে ক্লিক করুন।");
        }

        // /stop এলে সংযোগ বিচ্ছিন্ন করা
        if ($messageText === '/stop') {
            $user = User::where('telegram_chat_id', (string) $chatId)->first();
            if ($user) {
                $user->update([
                    'telegram_chat_id'       => null,
                    'telegram_connected_at'  => null,
                ]);
                $this->telegram->send($chatId, "❌ <b>সংযোগ বিচ্ছিন্ন করা হয়েছে।</b>\n\nআপনি আর রক্তদূতের কোনো নোটিফিকেশন পাবেন না। পুনরায় যুক্ত হতে প্রোফাইল থেকে আবার কানেক্ট করুন।");
            }
        }

        return response()->json(['ok' => true]);
    }

    private function hasValidWebhookSecret(Request $request): bool
    {
        $expected = (string) config('services.telegram.webhook_secret', '');
        if ($expected === '') {
            return false;
        }

        $provided = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');
        if ($provided === '') {
            return false;
        }

        return hash_equals($expected, $provided);
    }

    // ─────────────────────────────────────────────────────────────
    // ৩. Verification Logic — Token মিলিয়ে chat_id সেভ করা
    // ─────────────────────────────────────────────────────────────
    private function handleVerification(int|string $chatId, string $token): void
    {
        // ডোনার খোঁজা
        $user = User::where('telegram_verify_token', $token)->first();

        if (!$user) {
            $this->telegram->send($chatId, "❌ <b>যাচাইকরণ ব্যর্থ হয়েছে।</b>\n\nলিঙ্কটি মেয়াদোত্তীর্ণ বা ভুল। দয়া করে প্রোফাইল থেকে আবার চেষ্টা করুন।");
            return;
        }

        // Chat ID সেভ এবং token পরিষ্কার করা
        $user->update([
            'telegram_chat_id'       => (string) $chatId,
            'telegram_connected_at'  => now(),
            'telegram_verify_token'  => null,
        ]);

        // সফল সংযোগের ওয়েলকাম মেসেজ
        $this->telegram->sendWelcomeMessage($chatId, $user->name);
    }

    // ─────────────────────────────────────────────────────────────
    // ৪. Disconnect Telegram (profile থেকে)
    // ─────────────────────────────────────────────────────────────
    public function disconnect(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->user()->update([
            'telegram_chat_id'      => null,
            'telegram_connected_at' => null,
            'telegram_verify_token' => null,
        ]);

        return back()->with('success', '✅ টেলিগ্রাম সংযোগ বিচ্ছিন্ন করা হয়েছে।');
    }

    // ─────────────────────────────────────────────────────────────
    // ৫. Artisan Command / Webhook Setup (Admin Only)
    // ─────────────────────────────────────────────────────────────
    public function setWebhook(Request $request): \Illuminate\Http\JsonResponse
    {
        abort_unless(app()->environment('local') || $request->user()?->hasRole('admin'), 403);

        $url    = route('telegram.webhook');
        $result = $this->telegram->setWebhook($url);

        return response()->json($result);
    }
}
