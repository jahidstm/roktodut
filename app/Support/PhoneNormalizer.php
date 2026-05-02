<?php

namespace App\Support;

class PhoneNormalizer
{
    /**
     * Normalize a Bangladesh phone number into local mobile format when possible.
     *
     * Examples:
     * - '+880 1712-345678'    => '01712345678'
     * - '880(1912)345678'     => '01912345678'
     * - '০১৭১২-৩৪৫৬৭৮'        => '01712345678'
     * - 'call: 01712 34 5678' => '01712345678'
     * - '123-abc'             => '123'
     */
    public static function normalizeBdPhone(string $raw): string
    {
        $englishDigits = strtr($raw, [
            '০' => '0',
            '১' => '1',
            '২' => '2',
            '৩' => '3',
            '৪' => '4',
            '৫' => '5',
            '৬' => '6',
            '৭' => '7',
            '৮' => '8',
            '৯' => '9',
        ]);

        $withoutCommonSeparators = preg_replace('/[\s\-\(\)]+/u', '', $englishDigits) ?? '';
        $digits = preg_replace('/\D+/', '', $withoutCommonSeparators) ?? '';

        if (preg_match('/^8801\d{9}$/', $digits) === 1) {
            $digits = '0' . substr($digits, 3);
        }

        if (preg_match('/^01\d{9}$/', $digits) === 1) {
            return $digits;
        }

        return $digits;
    }
}
