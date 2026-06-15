<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// প্রতি ১৫ মিনিটে পুরোনো pending/in_progress রিকোয়েস্ট expired করা হবে
Schedule::command('requests:expire')->everyFifteenMinutes();

Schedule::command('donations:auto-approve')->hourly();
Schedule::command('subscriptions:dispatch-requests')->hourly();
Schedule::command('donor:dfi-decay')->daily();
Schedule::command('nid:purge-expired')->daily();

// ── Smart Cooldown Reminder — প্রতিদিন সকাল ৮টায় ──────────────────────────
// State machine: stage 0→1 (7-day), 1→2 (3-day), 2→3 (day-of)
// cooldown_until reset করলে GamificationService থেকে stage 0 করা হয়।
Schedule::command('reminders:send-cooldown')
    ->dailyAt('08:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground();

// Queue worker fallback (shared hosting / no Supervisor)
// IMPORTANT: Server-এ cron দিয়ে `php artisan schedule:run` চালু থাকতে হবে।
Schedule::command('queue:work --stop-when-empty --sleep=1 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
