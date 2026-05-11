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
