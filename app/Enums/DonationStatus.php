<?php

namespace App\Enums;

enum DonationStatus: string
{
    case PENDING       = 'pending';
    case CONFIRMED     = 'confirmed';
    case DISPUTED      = 'disputed';
    case AUTO_APPROVED = 'auto_approved';

    public function label(): string
    {
        return match ($this) {
            self::PENDING       => 'অপেক্ষমাণ',
            self::CONFIRMED     => 'নিশ্চিত',
            self::DISPUTED      => 'আপত্তিকৃত',
            self::AUTO_APPROVED => 'স্বয়ংক্রিয় অনুমোদিত',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING       => 'bg-yellow-100 text-yellow-800',
            self::CONFIRMED     => 'bg-green-100 text-green-800',
            self::DISPUTED      => 'bg-red-100 text-red-800',
            self::AUTO_APPROVED => 'bg-blue-100 text-blue-800',
        };
    }
}
