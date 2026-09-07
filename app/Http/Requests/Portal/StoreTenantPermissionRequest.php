<?php

namespace App\Http\Requests\Portal;

use App\Enums\PermissionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTenantPermissionRequest extends FormRequest
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
            'type' => ['required', new Enum(PermissionType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'start_at' => [
                'nullable',
                'date',
                'required_if:type,'.PermissionType::GuestStay->value,
                'required_if:type,'.PermissionType::LateReturn->value,
            ],
            'end_at' => [
                'nullable',
                'date',
                'after:start_at',
                'required_if:type,'.PermissionType::GuestStay->value,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Jenis permohonan izin wajib dipilih.',
            'type.Illuminate\Validation\Rules\Enum' => 'Jenis permohonan izin yang dipilih tidak valid.',
            'title.required' => 'Judul permohonan izin wajib diisi.',
            'title.max' => 'Judul permohonan maksimal 255 karakter.',
            'description.required' => 'Deskripsi permohonan izin wajib diisi.',
            'description.max' => 'Deskripsi permohonan maksimal 1000 karakter.',
            'start_at.required_if' => 'Tanggal/waktu mulai wajib diisi untuk jenis izin ini.',
            'start_at.date' => 'Format tanggal/waktu mulai tidak valid.',
            'end_at.required_if' => 'Tanggal/waktu selesai wajib diisi untuk jenis izin ini.',
            'end_at.date' => 'Format tanggal/waktu selesai tidak valid.',
            'end_at.after' => 'Tanggal/waktu selesai harus setelah tanggal/waktu mulai.',
        ];
    }
}
