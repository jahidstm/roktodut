<?php

namespace App\Enums;

enum UserRole: string
{
    case DONOR     = 'donor';
    case RECIPIENT = 'recipient';
    case ORG_ADMIN = 'org_admin';
    case ADMIN     = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::DONOR     => 'রক্তদাতা',
            self::RECIPIENT => 'গ্রহীতা',
            self::ORG_ADMIN => 'প্রতিষ্ঠান প্রশাসক',
            self::ADMIN     => 'কর্তৃপক্ষ',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::ADMIN     => 'admin.dashboard',
            self::ORG_ADMIN => 'org.dashboard',
            default         => 'dashboard',
        };
    }
}
