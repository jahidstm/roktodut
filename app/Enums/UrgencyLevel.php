<?php

namespace App\Enums;

enum UrgencyLevel: string
{
    case NORMAL    = 'normal';
    case URGENT    = 'urgent';
    case EMERGENCY = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::NORMAL    => 'সাধারণ',
            self::URGENT    => 'জরুরি',
            self::EMERGENCY => 'অতি জরুরি',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NORMAL    => 'bg-gray-100 text-gray-800',
            self::URGENT    => 'bg-yellow-100 text-yellow-800',
            self::EMERGENCY => 'bg-red-100 text-red-800 animate-pulse',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::NORMAL    => '🟢',
            self::URGENT    => '🟡',
            self::EMERGENCY => '🔴',
        };
    }
}
