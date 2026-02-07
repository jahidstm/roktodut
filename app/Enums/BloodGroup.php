<?php

namespace App\Enums;

enum BloodGroup: string
{
    case A_POS = 'A+';
    case A_NEG = 'A-';
    case B_POS = 'B+';
    case B_NEG = 'B-';
    case AB_POS = 'AB+';
    case AB_NEG = 'AB-';
    case O_POS = 'O+';
    case O_NEG = 'O-';

    public function label(): string
    {
        return $this->value; // ব্লাড গ্রুপ ইংরেজিতেই সুন্দর দেখায়
    }
}
