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
Schedule::command('nid:purge-expired')->daily();

// Queue worker fallback (shared hosting / no Supervisor)
// IMPORTANT: Server-এ cron দিয়ে `php artisan schedule:run` চালু থাকতে হবে।
Schedule::command('queue:work --stop-when-empty --sleep=1 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
