<?php

namespace App\Services;

use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class VerifyPaymentService
{
    /**
     * Memverifikasi pembayaran dan mengecek apakah tagihan sudah lunas atau dibayar sebagian.
     */
    public function verify(Payment $payment, int $verifierId): Payment
    {
        return DB::transaction(function () use ($payment, $verifierId) {
            if ($payment->status !== StatusPembayaran::Pending) {
                throw new InvalidArgumentException('Hanya pembayaran berstatus pending yang bisa diverifikasi.');
            }

            // Update status pembayaran
            $payment->update([
                'status' => StatusPembayaran::Verified,
                'verified_by' => $verifierId,
            ]);

            // Ambil invoice terkait
            $invoice = $payment->invoice;

            // Hitung total semua pembayaran verified untuk invoice ini
            $totalVerified = (float) $invoice->payments()
                ->where('status', StatusPembayaran::Verified)
                ->sum('amount');

            // Update status invoice sesuai total verified
            if ($totalVerified >= (float) $invoice->total_amount) {
                $invoice->update(['status' => StatusTagihan::Paid]);
            } elseif ($totalVerified > 0) {
                $invoice->update(['status' => StatusTagihan::PartiallyPaid]);
            }

            return $payment;
        });
    }

    /**
     * Menolak pembayaran. Status tagihan dibiarkan seperti aslinya.
     */
    public function reject(Payment $payment, int $verifierId, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($payment, $verifierId, $notes) {
            if ($payment->status !== StatusPembayaran::Pending) {
                throw new InvalidArgumentException('Hanya pembayaran berstatus pending yang bisa ditolak.');
            }

            $payment->update([
                'status' => StatusPembayaran::Rejected,
                'verified_by' => $verifierId,
                'notes' => $notes ?: $payment->notes, // Simpan atau update notes alasan penolakan
            ]);

            return $payment;
        });
    }
}
