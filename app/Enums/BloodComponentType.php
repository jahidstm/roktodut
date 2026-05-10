<?php

namespace App\Enums;

enum BloodComponentType: string
{
    case WHOLE_BLOOD = 'whole_blood';
    case PACKED_RBC = 'packed_rbc';
    case PLATELETS = 'platelets';
    case PLASMA = 'plasma';

    public function label(): string
    {
        return match ($this) {
            self::WHOLE_BLOOD => 'পূর্ণ রক্ত (Whole Blood)',
            self::PACKED_RBC => 'রেড সেল / PRBC',
            self::PLATELETS => 'প্লাটিলেট (Apheresis)',
            self::PLASMA => 'প্লাজমা',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::WHOLE_BLOOD => 'Whole Blood',
            self::PACKED_RBC => 'PRBC',
            self::PLATELETS => 'Platelets',
            self::PLASMA => 'Plasma',
        };
    }
}
