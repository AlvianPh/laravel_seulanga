<?php

namespace App\Http\Requests;

use App\Enums\RoleUser;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewTenantDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->role === RoleUser::Owner || $this->user()->role === RoleUser::Admin);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'in:verify,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Aksi verifikasi atau penolakan wajib dipilih.',
            'action.in' => 'Aksi verifikasi tidak valid.',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika dokumen ditolak.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}
