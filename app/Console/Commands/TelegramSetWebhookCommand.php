<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramSetWebhookCommand extends Command
{
    protected $signature   = 'telegram:set-webhook';
    protected $description = 'Telegram Bot Webhook সেট করুন (Production-এ একবার চালাতে হবে)';

    public function handle(TelegramService $telegram): void
    {
        $webhookUrl = route('telegram.webhook');

        $this->info("Webhook URL: {$webhookUrl}");

        $result = $telegram->setWebhook($webhookUrl);

        if (($result['ok'] ?? false) === true) {
            $this->info('✅ Webhook সফলভাবে সেট হয়েছে!');
            $this->line('Description: ' . ($result['description'] ?? 'OK'));
        } else {
            $this->error('❌ Webhook সেট করতে সমস্যা হয়েছে।');
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
}
