<?php

namespace App\Enums;

enum UrgencyLevel: string
{
    case NORMAL = 'normal';
    case URGENT = 'urgent';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::NORMAL => 'সাধারণ',
            self::URGENT => 'জরুরি',
            self::CRITICAL => 'মরণাপন্ন',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NORMAL => 'text-gray-600',
            self::URGENT => 'text-orange-600',
            self::CRITICAL => 'text-red-600 font-bold',
        };
    }
}
