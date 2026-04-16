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
            'website.max'      => 'অবৈধ অনুরোধ।',

            // নাম
            'name.required'    => 'আপনার নাম দেওয়া বাধ্যতামূলক।',
            'name.min'         => 'নাম কমপক্ষে ২ অক্ষরের হতে হবে।',
            'name.max'         => 'নাম সর্বোচ্চ ১২০ অক্ষরের হতে পারবে।',

            // ইমেইল
            'email.required'   => 'ইমেইল ঠিকানা দেওয়া বাধ্যতামূলক।',
            'email.email'      => 'সঠিক ইমেইল ঠিকানা দিন।',
            'email.max'        => 'ইমেইল ঠিকানা বড় হয়ে গেছে।',

            // ফোন
            'phone.max'        => 'ফোন নম্বর সর্বোচ্চ ২০ সংখ্যার হতে পারবে।',

            // বিষয়
            'subject.required' => 'বার্তার বিষয় দেওয়া বাধ্যতামূলক।',
            'subject.min'      => 'বিষয় কমপক্ষে ৫ অক্ষরের হতে হবে।',
            'subject.max'      => 'বিষয় সর্বোচ্চ ১২০ অক্ষরের হতে পারবে।',

            // বার্তা
            'message.required' => 'বার্তার মূল বিষয়বস্তু লেখা বাধ্যতামূলক।',
            'message.min'      => 'বার্তা কমপক্ষে ২০ অক্ষরের হতে হবে।',
            'message.max'      => 'বার্তা সর্বোচ্চ ২০০০ অক্ষরের হতে পারবে।',
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
