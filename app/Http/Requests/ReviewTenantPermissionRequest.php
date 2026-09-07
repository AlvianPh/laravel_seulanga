<?php

namespace App\Http\Requests;

use App\Enums\RoleUser;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewTenantPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && in_array($this->user()->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'review_note' => ['required_if:action,reject', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Keputusan review permohonan izin wajib dipilih.',
            'action.in' => 'Keputusan review harus berupa setujui (approve) atau tolak (reject).',
            'review_note.required_if' => 'Catatan alasan penolakan wajib diisi ketika permohonan ditolak.',
            'review_note.max' => 'Catatan review maksimal 1000 karakter.',
        ];
    }
}
