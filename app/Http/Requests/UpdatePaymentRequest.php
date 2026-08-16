<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0',
            'coment' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'voucher' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ];
    }
}
