<?php

namespace App\Http\Requests;

use App\Enums\RoleUser;
use App\Enums\StatusMaintenance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMaintenanceStatusRequest extends FormRequest
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
            'status' => ['required', new Enum(StatusMaintenance::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => [
                'required_if:status,'.StatusMaintenance::Rejected->value,
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status perbaikan wajib dipilih.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status perbaikan yang dipilih tidak valid.',
            'notes.max' => 'Catatan pengerjaan maksimal 1000 karakter.',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika status ditolak.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}
