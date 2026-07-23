<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kost_name' => ['required', 'string', 'max:255'],
            'kost_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'kost_address' => ['nullable', 'string'],
            'default_due_date_day' => ['required', 'integer', 'min:1', 'max:28'],
            'default_late_fee_id' => ['nullable', 'exists:additional_fee_types,id'],
            'default_bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
        ];
    }
}
