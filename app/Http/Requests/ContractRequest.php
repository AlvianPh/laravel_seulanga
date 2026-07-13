<?php

namespace App\Http\Requests;

use App\Enums\StatusKamar;
use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Policy di Controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $contractId = $this->route('contract') ? $this->route('contract')->id : null;

        $rules = [
            'tenant_id'      => ['required', 'exists:tenants,id'],
            'room_id'        => ['required', 'exists:rooms,id'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after:start_date'],
            'rent_price'     => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ];

        // Validasi kombinasi unik
        $rules['start_date'][] = Rule::unique('contracts')->where(function ($query) {
            return $query->where('tenant_id', $this->tenant_id)
                         ->where('room_id', $this->room_id)
                         ->where('start_date', $this->start_date);
        })->ignore($contractId);

        return $rules;
    }

    /**
     * Penyesuaian validasi ekstra sesudah validasi basic.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                // Jangan cek ketersediaan kamar jika sedang update kontrak lama
                // Cek hanya saat method POST (Create)
                if ($this->isMethod('post') && $this->room_id) {
                    $room = Room::find($this->room_id);
                    if ($room && $room->status !== StatusKamar::Available) {
                        $validator->errors()->add(
                            'room_id',
                            'Kamar yang dipilih tidak tersedia (sedang ' . $room->status->value . ').'
                        );
                    }
                }
            }
        ];
    }

    /**
     * Custom attributes.
     */
    public function attributes(): array
    {
        return [
            'tenant_id'      => 'Penghuni',
            'room_id'        => 'Kamar',
            'start_date'     => 'Tanggal Mulai',
            'end_date'       => 'Tanggal Selesai',
            'rent_price'     => 'Harga Sewa',
            'deposit_amount' => 'Jumlah Deposit',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.unique' => 'Kontrak dengan kombinasi Penghuni, Kamar, dan Tanggal Mulai ini sudah ada.',
            'end_date.after'    => 'Tanggal selesai harus lebih besar dari tanggal mulai.',
        ];
    }
}
