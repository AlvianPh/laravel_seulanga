<?php

namespace App\Http\Requests;

use App\Enums\JenisKelamin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
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
        $tenantId = $this->route('tenant') ? $this->route('tenant')->id : null;

        return [
            'name'  => ['required', 'string', 'max:255'],
            'nik'   => [
                'required',
                'digits:16', // Harus tepat 16 digit angka
                Rule::unique('tenants')->ignore($tenantId),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(08|628)\d+$/', // Format no HP Indonesia
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('tenants')->ignore($tenantId),
            ],
            'gender'                  => ['required', Rule::enum(JenisKelamin::class)],
            'birth_date'              => ['nullable', 'date'],
            'address'                 => ['nullable', 'string', 'max:1000'],
            'ktp_photo'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'tenant_photo'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'emergency_contact_name'  => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20', 'regex:/^(08|628)\d+$/'],
        ];
    }

    /**
     * Custom error messages for specific rules.
     */
    public function messages(): array
    {
        return [
            'nik.digits'                => 'NIK harus tepat 16 digit angka.',
            'phone.regex'               => 'Format nomor telepon harus diawali dengan 08 atau 628.',
            'emergency_contact_phone.regex' => 'Format nomor telepon darurat harus diawali dengan 08 atau 628.',
        ];
    }

    /**
     * Custom attributes.
     */
    public function attributes(): array
    {
        return [
            'name'                    => 'Nama Lengkap',
            'nik'                     => 'NIK (Nomor Induk Kependudukan)',
            'phone'                   => 'Nomor HP',
            'email'                   => 'Alamat Email',
            'gender'                  => 'Jenis Kelamin',
            'birth_date'              => 'Tanggal Lahir',
            'address'                 => 'Alamat Asal',
            'ktp_photo'               => 'Foto KTP',
            'tenant_photo'            => 'Foto Penghuni',
            'emergency_contact_name'  => 'Nama Kontak Darurat',
            'emergency_contact_phone' => 'Nomor Kontak Darurat',
        ];
    }
}
