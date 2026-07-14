<?php

namespace App\Services;

use App\Enums\StatusKontrak;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceService
{
    /**
     * Generate tagihan bulanan untuk semua kontrak yang sedang aktif.
     * Tidak akan menduplikasi jika tagihan untuk bulan & kontrak yang sama sudah ada.
     *
     * @param int $month Bulan (1-12)
     * @param int $year Tahun (contoh: 2026)
     * @return int Jumlah tagihan yang berhasil dibuat
     */
    public function generateMonthlyInvoices(int $month, int $year): int
    {
        $activeContracts = Contract::where('status', StatusKontrak::Active)->get();
        $generatedCount = 0;

        foreach ($activeContracts as $contract) {
            // Cek duplikasi
            $exists = Invoice::where('contract_id', $contract->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if ($exists) {
                continue;
            }

            // Tentukan due_date: tanggal 10 bulan berjalan
            $dueDate = Carbon::create($year, $month, 10)->format('Y-m-d');

            Invoice::create([
                'contract_id'     => $contract->id,
                'tenant_id'       => $contract->tenant_id,
                'room_id'         => $contract->room_id,
                'year'            => $year,
                'month'           => $month,
                'rent_amount'     => $contract->rent_price, // Copy snapshot harga sewa
                'electricity_fee' => 0,
                'water_fee'       => 0,
                'internet_fee'    => 0,
                'penalty_fee'     => 0,
                'other_fee'       => 0,
                'total_amount'    => $contract->rent_price, // Di awal total = rent_amount
                'due_date'        => $dueDate,
                'status'          => StatusTagihan::Pending,
            ]);

            $generatedCount++;
        }

        Log::info("Generated {$generatedCount} invoices for {$month}/{$year}.");

        return $generatedCount;
    }

    /**
     * Cek tagihan pending yang sudah lewat jatuh tempo, dan ubah status jadi overdue.
     *
     * @return int Jumlah tagihan yang di-update
     */
    public function markOverdueInvoices(): int
    {
        $today = Carbon::today()->format('Y-m-d');

        $updatedCount = Invoice::where('status', StatusTagihan::Pending)
            ->where('due_date', '<', $today)
            ->update(['status' => StatusTagihan::Overdue]);

        if ($updatedCount > 0) {
            Log::info("Marked {$updatedCount} invoices as overdue.");
        }

        return $updatedCount;
    }
}
