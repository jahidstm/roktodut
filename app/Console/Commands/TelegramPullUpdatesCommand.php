<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramPullUpdatesCommand extends Command
{
    protected $signature = 'telegram:pull';
    protected $description = 'Local testing: Pull updates from Telegram using getUpdates (No Webhook needed)';

    public function handle(TelegramService $telegramService): void
    {
        $token = config('services.telegram.token');
        if (empty($token)) {
            $this->error('TELEGRAM_BOT_TOKEN is not set in .env');
            return;
        }

        $this->info("Fetching updates from Telegram...");

        // ১. Webhook ডিলিট করতে হবে, না হলে getUpdates কাজ করবে না
        Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

        // ২. getUpdates কল করা
        $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates");
        
        if (!$response->successful()) {
            $this->error('Failed to connect to Telegram API');
            return;
        }

        $updates = $response->json('result') ?? [];
        
        if (empty($updates)) {
            $this->info("No new messages found.");
            return;
        }

        $processedCount = 0;
        $latestUpdateId = 0;

        foreach ($updates as $update) {
            $latestUpdateId = $update['update_id'];
            $messageText = $update['message']['text'] ?? '';
            $chatId = $update['message']['chat']['id'] ?? null;
            
            if (!$chatId) continue;

            if (str_starts_with($messageText, '/start verify_')) {
                $verifyToken = str_replace('/start verify_', '', $messageText);
                
                $user = User::where('telegram_verify_token', $verifyToken)->first();
                if ($user) {
                    $user->update([
                        'telegram_chat_id' => (string) $chatId,
                        'telegram_connected_at' => now(),
                        'telegram_verify_token' => null,
                    ]);
                    $telegramService->sendWelcomeMessage($chatId, $user->name);
                    $this->info("✅ User {$user->name} connected successfully!");
                    $processedCount++;
                } else {
                    $telegramService->send($chatId, "❌ টোকেনটি মেয়াদোত্তীর্ণ বা ভুল।");
                }
            } elseif ($messageText === '/start') {
                $telegramService->send($chatId, "🩸 রক্তদূত বটে স্বাগতম! প্রোফাইল থেকে Connect করুন।");
                $processedCount++;
            }
        }

        // ৩. Update offset so we don't process the same messages again
        if ($latestUpdateId > 0) {
            Http::get("https://api.telegram.org/bot{$token}/getUpdates", [
                'offset' => $latestUpdateId + 1
            ]);
        }

        $this->info("Done! Processed {$processedCount} new messages.");
    }
}
