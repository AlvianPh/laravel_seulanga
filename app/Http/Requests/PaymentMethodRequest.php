<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Dihandle oleh Policy
    }

    public function rules(): array
    {
        $id = $this->route('payment_method') ? $this->route('payment_method')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods', 'name')->ignore($id),
            ],
        ];
    }
}
