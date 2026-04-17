<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreContactMessageRequest
 *
 * যোগাযোগ ফর্মের ভ্যালিডেশন ও অথরাইজেশন।
 * - গেস্ট ইউজার: name required
 * - লগইন ইউজার: name optional (users টেবিল থেকে নেওয়া হবে)
 * - Honeypot: 'website' ফিল্ড ফাঁকা থাকতে হবে
 * - সকল এরর বাংলায়
 */
class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // থ্রটল + honeypot কন্ট্রোলারেই হ্যান্ডল হবে
    }

    public function rules(): array
    {
        return [
            // ─── Honeypot ──────────────────────────────────────────────────
            // বট-ফিলড: মানুষ এই ফিল্ড দেখতে পাবে না (CSS hidden)
            'website'  => ['nullable', 'string', 'max:0'],

            // ─── প্রেরকের তথ্য ─────────────────────────────────────────────
            'name'     => [
                $this->user() ? 'nullable' : 'required',
                'string',
                'min:2',
                'max:120',
            ],
            'email'    => ['required', 'email:rfc,dns', 'max:180'],
            'phone'    => ['nullable', 'string', 'max:20'],

            // ─── বার্তার বিষয়বস্তু ────────────────────────────────────────
            'subject'  => ['required', 'string', 'min:5', 'max:120'],
            'message'  => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            // Honeypot — দেখানো হবে না (বট ধরার জন্য silent reject)
            'website.max'      => __('contact.validation.honeypot'),

            // নাম
            'name.required'    => __('contact.validation.name_required'),
            'name.min'         => __('contact.validation.name_min'),
            'name.max'         => __('contact.validation.name_max'),

            // ইমেইল
            'email.required'   => __('contact.validation.email_required'),
            'email.email'      => __('contact.validation.email_email'),
            'email.max'        => __('contact.validation.email_max'),

            // ফোন
            'phone.max'        => __('contact.validation.phone_max'),

            // বিষয়
            'subject.required' => __('contact.validation.subject_required'),
            'subject.min'      => __('contact.validation.subject_min'),
            'subject.max'      => __('contact.validation.subject_max'),

            // বার্তা
            'message.required' => __('contact.validation.message_required'),
            'message.min'      => __('contact.validation.message_min'),
            'message.max'      => __('contact.validation.message_max'),

            // Throttle exceeded (HTTP 429 — ThrottleRequests middleware)
            // web.php: throttle:contact-guest / throttle:contact-auth
            // Laravel এই key দেখে: auth::throttle
            'auth.throttle'    => __('contact.validation.throttle', [
                'seconds' => ':seconds',
                'minutes' => ':minutes',
            ]),
        ];
    }

    public function attributes(): array
    {
        return [
            'name'    => 'নাম',
            'email'   => 'ইমেইল',
            'phone'   => 'ফোন',
            'subject' => 'বিষয়',
            'message' => 'বার্তা',
        ];
    }
}
