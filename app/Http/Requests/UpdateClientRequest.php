<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('id');

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip|unique:cliente,ip,' . $clientId,
            'phone' => 'required|string|max:20',
            'contracts_id' => 'required|exists:plan,id',
            'access_point_id' => 'required|exists:accesspoint,id',
            'street_address' => 'required|string|max:255',
            'file_upload' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return (new StoreClientRequest())->messages();
    }
}
