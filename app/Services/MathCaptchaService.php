<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Session;

class MathCaptchaService
{
    public function generate(): string
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        Session::put('captcha.answer', $a + $b);
        Session::put('captcha.expires_at', now()->addMinutes(5));

        return $this->toBanglaDigit((string) $a) . ' + ' . $this->toBanglaDigit((string) $b) . ' = ?';
    }

    public function verify($input): bool
    {
        $expiresAt = Session::get('captcha.expires_at');

        if (!($expiresAt instanceof CarbonInterface) || now()->greaterThan($expiresAt)) {
            return false;
        }

        $answer = Session::get('captcha.answer');
        $normalizedInput = $this->normalizeDigits((string) $input);

        if (!is_numeric($normalizedInput) || (int) $normalizedInput !== (int) $answer) {
            return false;
        }

        Session::forget(['captcha.answer', 'captcha.expires_at']);

        return true;
    }

    private function toBanglaDigit(string $number): string
    {
        return strtr($number, [
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

    private function normalizeDigits(string $value): string
    {
        $englishDigits = strtr($value, [
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

        return preg_replace('/\D+/', '', $englishDigits) ?? '';
    }
}
