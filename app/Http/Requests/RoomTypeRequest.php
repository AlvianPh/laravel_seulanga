<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * RoomTypeRequest — validasi input untuk create/update tipe kamar.
 */
class RoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $roomTypeId = $this->route('room_type') ? $this->route('room_type')->id : null;

        return [
            'name'          => [
                'required',
                'string',
                'max:100',
                Rule::unique('room_types')->ignore($roomTypeId),
            ],
            'description'   => ['nullable', 'string', 'max:500'],
            'default_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'          => 'Nama Tipe',
            'description'   => 'Deskripsi',
            'default_price' => 'Harga Rekomendasi',
        ];
    }
}
