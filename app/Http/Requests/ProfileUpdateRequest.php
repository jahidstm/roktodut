<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique(\App\Models\User::class)->ignore($this->user()->id)],
            
            // 🎯 নতুন যুক্ত করা ডোনার ফিল্ডস
            'phone' => ['nullable', 'string', 'max:20'],
            'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'district' => ['nullable', 'string', 'max:100'],
            'thana' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'weight' => ['nullable', 'numeric', 'min:30', 'max:200'], // ওজন ৩০ থেকে ২০০ কেজির মধ্যে
        ];
    }
}
