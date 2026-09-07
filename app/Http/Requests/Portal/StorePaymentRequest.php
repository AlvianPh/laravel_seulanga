<?php

namespace App\Http\Requests\Portal;

use App\Models\Invoice;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->tenant !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payment_date' => ['required', 'date'],
            'proof_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Validasi tambahan agar nominal pembayaran tidak melebihi sisa tagihan.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $invoice = $this->route('invoice');
            if ($invoice instanceof Invoice) {
                $remaining = $invoice->remainingBalance();
                if ($this->filled('amount') && (float) $this->input('amount') > $remaining) {
                    $validator->errors()->add(
                        'amount',
                        'Nominal pembayaran tidak boleh melebihi sisa tagihan (Rp '.number_format($remaining, 0, ',', '.').').'
                    );
                }
            }
        });
    }

    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal pembayaran wajib diisi.',
            'amount.numeric' => 'Nominal pembayaran harus berupa angka.',
            'amount.min' => 'Nominal pembayaran minimal Rp 1.',
            'payment_method_id.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method_id.exists' => 'Metode pembayaran yang dipilih tidak valid.',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date' => 'Format tanggal pembayaran tidak valid.',
            'proof_photo.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_photo.mimes' => 'Bukti pembayaran harus berformat JPG, JPEG, PNG, atau PDF.',
            'proof_photo.max' => 'Ukuran file bukti pembayaran maksimal 2MB.',
        ];
    }
}
