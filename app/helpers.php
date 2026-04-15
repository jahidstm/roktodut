<?php

use App\Support\BanglaDate;
use Carbon\CarbonInterface;

if (!function_exists('bn_datetime')) {
    function bn_datetime(?CarbonInterface $date, string $fallback = 'যত দ্রুত সম্ভব'): string
    {
        return BanglaDate::absolute($date, $fallback);
    }
}

if (!function_exists('bn_relative')) {
    function bn_relative(?CarbonInterface $date, string $fallback = 'এইমাত্র'): string
    {
        return BanglaDate::relative($date, $fallback);
    }
}
