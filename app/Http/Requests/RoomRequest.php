<?php

namespace App\Http\Requests;

use App\Enums\StatusKamar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * RoomRequest — validasi input untuk create/update data kamar.
 */
class RoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Otorisasi sudah ditangani oleh Policy via Controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roomId = $this->route('room') ? $this->route('room')->id : null;

        return [
            'room_number'   => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms')->ignore($roomId),
            ],
            'floor'         => ['required', 'integer', 'min:1'],
            'room_type_id'  => ['required', 'integer', 'exists:room_types,id'],
            'size_m2'       => ['required', 'numeric', 'min:0'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'deposit_price' => ['required', 'numeric', 'min:0'],
            'status'        => ['required', Rule::enum(StatusKamar::class)],
            'facilities'    => ['nullable', 'array'],
            'facilities.*'  => ['integer', 'exists:facilities,id'],

            // Validasi upload foto (multiple)
            'photos'        => ['nullable', 'array', 'max:5'], // Maksimal 5 foto per upload
            'photos.*'      => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maksimal 2MB per foto
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'room_number'   => 'Nomor Kamar',
            'floor'         => 'Lantai',
            'room_type_id'  => 'Tipe Kamar',
            'size_m2'       => 'Luas (m2)',
            'monthly_price' => 'Harga Sewa per Bulan',
            'deposit_price' => 'Harga Deposit',
            'status'        => 'Status Kamar',
            'facilities'    => 'Fasilitas',
            'photos'        => 'Foto Kamar',
            'photos.*'      => 'File Foto',
        ];
    }
}
