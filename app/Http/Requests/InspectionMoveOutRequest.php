<?php

namespace App\Http\Requests;

use App\Enums\RoleUser;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InspectionMoveOutRequest extends FormRequest
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
            'room_condition' => ['required', 'string', 'max:50'],
            'damage_notes' => ['nullable', 'string', 'max:1000'],
            'damage_cost' => ['nullable', 'numeric', 'min:0'],
            'requires_room_maintenance' => ['nullable', 'boolean'],
            'inspection_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'room_condition.required' => 'Kondisi kamar hasil inspeksi wajib dipilih.',
            'damage_cost.numeric' => 'Estimasi biaya kerusakan harus berupa angka.',
            'damage_cost.min' => 'Estimasi biaya kerusakan tidak boleh bernilai negatif.',
            'damage_notes.max' => 'Rincian kerusakan maksimal 1000 karakter.',
            'inspection_notes.max' => 'Catatan inspeksi maksimal 1000 karakter.',
        ];
    }
}
