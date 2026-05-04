<?php

namespace App\Http\Requests;

use App\Enums\UrgencyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBloodRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_name'   => ['nullable', 'string', 'max:120'],
            'blood_group'    => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'hospital_id'    => ['nullable', 'integer', 'exists:hospitals,id'],
            'bags_needed'    => ['required', 'integer', 'min:1', 'max:10'],

            // 📍 রিলেশনাল লোকেশন ভ্যালিডেশন
            'division_id'    => ['required', 'integer', 'exists:divisions,id'],
            'district_id'    => ['required', 'integer', 'exists:districts,id'],
            'upazila_id'     => ['required', 'integer', 'exists:upazilas,id'],

            // 📍 Geospatial coordinates (optional — picked via Leaflet map)
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],

            'address'        => ['nullable', 'string', 'max:255'],
            'contact_name'   => ['nullable', 'string', 'max:120'],
            'contact_number' => ['required', 'string', 'max:30'],
            'urgency'        => ['required', Rule::enum(UrgencyLevel::class)],
            'needed_at'      => ['required', 'date'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'is_phone_hidden' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'blood_group.required' => 'রক্তের গ্রুপ দেওয়া বাধ্যতামূলক।',
            'division_id.required' => 'বিভাগ নির্বাচন করুন।',
            'district_id.required' => 'জেলা নির্বাচন করুন।',
            'upazila_id.required'  => 'উপজেলা নির্বাচন করুন।',
            'urgency.required'     => 'জরুরি অবস্থা সিলেক্ট করুন।',
            'needed_at.required'   => 'কবে রক্ত লাগবে তা উল্লেখ করুন।',
        ];
    }
}
