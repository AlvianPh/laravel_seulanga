<?php

namespace App\Http\Requests;

use App\Enums\RoleUser;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewMoveOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && in_array($this->user()->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'review_note' => ['nullable', 'string', 'max:1000', 'required_if:action,reject'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Keputusan review wajib dipilih (approve atau reject).',
            'action.in' => 'Keputusan review tidak valid.',
            'review_note.required_if' => 'Alasan penolakan wajib diisi saat menolak permohonan move-out.',
            'review_note.max' => 'Catatan review maksimal 1000 karakter.',
        ];
    }
}
