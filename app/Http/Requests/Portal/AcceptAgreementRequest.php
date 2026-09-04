<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

class AcceptAgreementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isTenant();
    }

    public function rules(): array
    {
        return [
            'terms_accepted' => ['required', 'accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'terms_accepted' => 'Persetujuan Tata Tertib & Ketentuan Sewa',
        ];
    }

    public function messages(): array
    {
        return [
            'terms_accepted.required' => 'Anda harus mencentang persetujuan tata tertib untuk melanjutkan.',
            'terms_accepted.accepted' => 'Anda harus mencentang persetujuan tata tertib untuk melanjutkan.',
        ];
    }
}
