<?php

namespace App\Http\Requests;

use App\Enums\StatusTagihan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'electricity_fee' => ['nullable', 'numeric', 'min:0'],
            'water_fee'       => ['nullable', 'numeric', 'min:0'],
            'internet_fee'    => ['nullable', 'numeric', 'min:0'],
            'penalty_fee'     => ['nullable', 'numeric', 'min:0'],
            'other_fee'       => ['nullable', 'numeric', 'min:0'],
            'status'          => ['required', Rule::enum(StatusTagihan::class)],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Pastikan nilai kosong/null diisi 0 agar mudah dihitung
        $this->merge([
            'electricity_fee' => $this->electricity_fee ?: 0,
            'water_fee'       => $this->water_fee ?: 0,
            'internet_fee'    => $this->internet_fee ?: 0,
            'penalty_fee'     => $this->penalty_fee ?: 0,
            'other_fee'       => $this->other_fee ?: 0,
        ]);
    }
}
