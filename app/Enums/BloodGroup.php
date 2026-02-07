<?php

namespace App\Enums;

enum BloodGroup: string
{
    case A_POS  = 'A+';
    case A_NEG  = 'A-';
    case B_POS  = 'B+';
    case B_NEG  = 'B-';
    case AB_POS = 'AB+';
    case AB_NEG = 'AB-';
    case O_POS  = 'O+';
    case O_NEG  = 'O-';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::A_POS, self::A_NEG   => 'bg-blue-100 text-blue-800',
            self::B_POS, self::B_NEG   => 'bg-green-100 text-green-800',
            self::AB_POS, self::AB_NEG => 'bg-purple-100 text-purple-800',
            self::O_POS, self::O_NEG   => 'bg-red-100 text-red-800',
        };
    }

    public function compatibleDonors(): array
    {
        return match ($this) {
            self::AB_POS => self::cases(),
            self::AB_NEG => [self::O_NEG, self::A_NEG, self::B_NEG, self::AB_NEG],
            self::A_POS  => [self::O_NEG, self::O_POS, self::A_NEG, self::A_POS],
            self::A_NEG  => [self::O_NEG, self::A_NEG],
            self::B_POS  => [self::O_NEG, self::O_POS, self::B_NEG, self::B_POS],
            self::B_NEG  => [self::O_NEG, self::B_NEG],
            self::O_POS  => [self::O_NEG, self::O_POS],
            self::O_NEG  => [self::O_NEG],
        };
    }
}
