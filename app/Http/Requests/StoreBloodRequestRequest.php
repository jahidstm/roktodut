<?php

namespace App\Http\Requests;

use App\Enums\UrgencyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBloodRequestRequest extends FormRequest
{
    /**
     * ইউজার এই রিকোয়েস্ট করার জন্য অথোরাইজড কি না।
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ভ্যালিডেশন রুলস।
     */
    public function rules(): array
    {
        return [
            'patient_name'   => ['nullable', 'string', 'max:120'],
            'blood_group'    => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'hospital'       => ['nullable', 'string', 'max:200'],
            'bags_needed'    => ['required', 'integer', 'min:1', 'max:10'],
            'district'       => ['required', 'string', 'max:100'],
            'thana'          => ['required', 'string', 'max:100'],
            'address'        => ['nullable', 'string', 'max:255'],
            'contact_name'   => ['nullable', 'string', 'max:120'],
            'contact_number' => ['required', 'string', 'max:30'],
            
            // এনাম-ভিত্তিক ভ্যালিডেশন যা আমরা কোপাইলটের সাজেশনে আপডেট করেছিলাম
            'urgency'        => ['required', Rule::enum(UrgencyLevel::class)],
            
            'needed_at'      => ['required', 'date'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * কাস্টম এরর মেসেজ (অপশনাল)।
     */
    public function messages(): array
    {
        return [
            'blood_group.required' => 'রক্তের গ্রুপ দেওয়া বাধ্যতামূলক।',
            'urgency.required'     => 'জরুরি অবস্থা সিলেক্ট করুন।',
            'needed_at.required'   => 'কবে রক্ত লাগবে তা উল্লেখ করুন।',
        ];
    }
}