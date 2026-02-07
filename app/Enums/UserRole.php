<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case DONOR = 'donor';
    case VOLUNTEER = 'volunteer';
    case ORGANIZATION = 'organization'; // মাইগ্রেশনে এটা ব্যবহার করা হয়েছে

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'এডমিন',
            self::DONOR => 'রক্তদাতা',
            self::VOLUNTEER => 'স্বেচ্ছাসেবী',
            self::ORGANIZATION => 'অর্গানাইজেশন',
        };
    }
}
