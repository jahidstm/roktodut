<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFcmTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'fcm_token' => filled($this->input('fcm_token'))
                ? trim((string) $this->input('fcm_token'))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'fcm_token' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
