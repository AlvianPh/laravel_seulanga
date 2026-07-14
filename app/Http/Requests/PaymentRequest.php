<?php

namespace App\Http\Requests;

use App\Enums\MetodePembayaran;
use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
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
        return [
            'invoice_id'   => ['required', 'exists:invoices,id'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'method'       => ['required', Rule::enum(MetodePembayaran::class)],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'proof_photo'  => [
                // Wajib upload bukti jika metodenya Transfer Bank atau QRIS
                Rule::requiredIf(function () {
                    return in_array($this->input('method'), [MetodePembayaran::Transfer->value, MetodePembayaran::Qris->value]);
                }),
                'nullable', // Jika cash, boleh null
                'image',
                'max:2048'
            ],
        ];
    }

    /**
     * Validasi custom tambahan setelah rule dasar selesai.
     */
    public function after(): array
    {
        return [
            function ($validator) {
                if ($this->invoice_id) {
                    $invoice = Invoice::find($this->invoice_id);
                    if ($invoice && !in_array($invoice->status->value, ['pending', 'overdue'])) {
                        $validator->errors()->add(
                            'invoice_id',
                            'Pembayaran hanya bisa dilakukan untuk tagihan yang masih Pending atau Overdue.'
                        );
                    }
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'proof_photo.required' => 'Bukti pembayaran wajib dilampirkan untuk metode Transfer atau QRIS.',
            'amount.min'           => 'Jumlah pembayaran harus lebih dari 0.',
        ];
    }
}
