<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Dihandle oleh Policy
    }

    public function rules(): array
    {
        $id = $this->route('expense_category') ? $this->route('expense_category')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expense_categories', 'name')->ignore($id),
            ],
        ];
    }
}
