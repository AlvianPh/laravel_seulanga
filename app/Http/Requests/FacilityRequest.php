<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FacilityRequest — validasi input untuk create/update fasilitas.
 */
class FacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $facilityId = $this->route('facility') ? $this->route('facility')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('facilities')->ignore($facilityId),
            ],
            'icon' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Fasilitas',
            'icon' => 'Ikon',
        ];
    }
}
