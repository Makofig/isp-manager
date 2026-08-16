<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccessPointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ssid' => 'required|string|max:255',
            'frequency' => 'required|string|max:100',
            'ip_address' => 'nullable|ip',
            'location' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ssid.required' => 'The SSID is required.',
            'frequency.required' => 'Frequency is mandatory.',
            'ip_address.ip' => 'The IP address must be valid.',
            'location.required' => 'Location is required.',
        ];
    }
}
