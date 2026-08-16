<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by policies in v2.0
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip|unique:cliente,ip',
            'phone' => 'required|string|max:20',
            'contracts_id' => 'required|exists:plan,id',
            'access_point_id' => 'required|exists:accesspoint,id',
            'street_address' => 'required|string|max:255',
            'file_upload' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'The name cannot be empty.',
            'last_name.required' => 'The surname cannot be empty.',
            'phone.required' => 'A phone number is required.',
            'contracts_id.required' => 'You must select a contract.',
            'contracts_id.exists' => 'The selected contract is not valid.',
            'access_point_id.required' => 'You must select an access point.',
            'access_point_id.exists' => 'The selected access point is not valid.',
            'street_address.required' => 'The address is required.',
            'file_upload.image' => 'The file must be a valid image.',
            'file_upload.max' => 'The image cannot exceed 2 MB.',
        ];
    }
}
