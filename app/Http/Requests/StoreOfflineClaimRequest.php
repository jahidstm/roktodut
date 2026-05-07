<?php

namespace App\Http\Requests;

use App\Support\PhoneNormalizer;
use Illuminate\Foundation\Http\FormRequest;

class StoreOfflineClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'recipient_phone' => [
                'required',
                'string',
                'max:30',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $normalized = PhoneNormalizer::normalizeBdPhone((string) $value);
                    if (preg_match('/^01\d{9}$/', $normalized) !== 1) {
                        $fail('সঠিক মোবাইল নম্বর দিন (যেমন: 01XXXXXXXXX)।');
                    }
                },
            ],
            'patient_name' => ['required', 'string', 'max:120'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'hospital_name' => ['nullable', 'string', 'max:180'],
            'donation_date' => ['required', 'date', 'before_or_equal:today'],
            'proof_path' => ['nullable', 'file', 'image', 'max:4096'],
        ];
    }
}
