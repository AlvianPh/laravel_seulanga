<?php

namespace App\Http\Requests\Portal;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMoveOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->user()->tenant !== null
            && $this->user()->tenant->activeContract() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requested_move_out_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'requested_move_out_date.required' => 'Tanggal rencana keluar kost wajib diisi.',
            'requested_move_out_date.date' => 'Format tanggal rencana keluar tidak valid.',
            'requested_move_out_date.after_or_equal' => 'Tanggal rencana keluar tidak boleh berada di masa lalu.',
            'reason.required' => 'Alasan keluar kost wajib diisi.',
            'reason.max' => 'Alasan keluar maksimal 1000 karakter.',
            'notes.max' => 'Catatan tambahan maksimal 1000 karakter.',
        ];
    }
}
