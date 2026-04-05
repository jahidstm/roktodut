<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// প্রতি ঘণ্টায় একবার সিস্টেম চেক করবে কোনো রিকোয়েস্ট এক্সপায়ার হয়েছে কি না
Schedule::command('requests:close-expired')->hourly();

Schedule::command('donations:auto-approve')->hourly();