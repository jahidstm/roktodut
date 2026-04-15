<?php

namespace App\Support;

use Carbon\CarbonInterface;

class BanglaDate
{
    /**
     * Format absolute datetime in Bangla style.
     */
    public static function absolute(?CarbonInterface $date, string $fallback = 'যত দ্রুত সম্ভব'): string
    {
        if (!$date) {
            return $fallback;
        }

        $formatted = $date->locale('bn')->translatedFormat('j F Y, g:i A');
        $formatted = strtr($formatted, [
            'AM' => 'পূর্বাহ্ণ',
            'PM' => 'অপরাহ্ণ',
            'January' => 'জানুয়ারি',
            'February' => 'ফেব্রুয়ারি',
            'March' => 'মার্চ',
            'April' => 'এপ্রিল',
            'May' => 'মে',
            'June' => 'জুন',
            'July' => 'জুলাই',
            'August' => 'আগস্ট',
            'September' => 'সেপ্টেম্বর',
            'October' => 'অক্টোবর',
            'November' => 'নভেম্বর',
            'December' => 'ডিসেম্বর',
        ]);

        return self::digits($formatted);
    }

    /**
     * Format relative time in Bangla style.
     */
    public static function relative(?CarbonInterface $date, string $fallback = 'এইমাত্র'): string
    {
        if (!$date) {
            return $fallback;
        }

        $relative = $date->locale('bn')->diffForHumans();

        return self::digits($relative);
    }

    public static function digits(string $text): string
    {
        return strtr($text, [
            '0' => '০',
            '1' => '১',
            '2' => '২',
            '3' => '৩',
            '4' => '৪',
            '5' => '৫',
            '6' => '৬',
            '7' => '৭',
            '8' => '৮',
            '9' => '৯',
        ]);
    }
}
