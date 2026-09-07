<?php

namespace App\Services;

use App\Enums\StatusApplication;
use App\Enums\StatusKamar;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantApplication;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * TenantApplicationService
 *
 * Menangani siklus hidup pengajuan sewa kamar oleh calon penghuni kost:
 *  - Pembuatan pengajuan (apply)
 *  - Persetujuan pengajuan oleh staf (approve)
 *  - Penolakan pengajuan oleh staf (reject)
 *  - Pembatalan mandiri oleh penghuni (cancel)
 *
 * ATURAN BISNIS:
 * Persetujuan pengajuan (approve) TIDAK otomatis membuat kontrak aktif.
 * Proses pembuatan kontrak dilakukan pada modul kontrak sewa selanjutnya.
 */
class TenantApplicationService
{
    /**
     * Mengajukan sewa kamar oleh tenant.
     */
    public function apply(Tenant $tenant, int $roomId, ?string $notes = null): TenantApplication
    {
        return DB::transaction(function () use ($tenant, $roomId, $notes) {
            // Cek apakah tenant sudah punya kontrak aktif
            if ($tenant->activeContract()) {
                throw new InvalidArgumentException('Anda sudah memiliki kontrak kamar yang sedang aktif.');
            }

            // Cek apakah tenant sudah punya pengajuan pending
            if ($tenant->pendingApplication()) {
                throw new InvalidArgumentException('Anda masih memiliki pengajuan sewa yang sedang diproses.');
            }

            // Cek ketersediaan kamar
            $room = Room::findOrFail($roomId);
            if ($room->status !== StatusKamar::Available) {
                throw new InvalidArgumentException('Kamar yang dipilih saat ini tidak tersedia untuk diajukan.');
            }

            return TenantApplication::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'status' => StatusApplication::Pending,
                'application_notes' => $notes,
            ]);
        });
    }

    /**
     * Menyetujui pengajuan sewa oleh staf (Admin/Owner).
     */
    public function approve(TenantApplication $application, int $reviewerId): TenantApplication
    {
        return DB::transaction(function () use ($application, $reviewerId) {
            if ($application->status !== StatusApplication::Pending) {
                throw new InvalidArgumentException('Hanya pengajuan berstatus pending yang dapat disetujui.');
            }

            // Validasi ulang ketersediaan kamar
            $room = $application->room;
            if ($room->status !== StatusKamar::Available) {
                throw new InvalidArgumentException('Kamar sudah tidak tersedia untuk diproses.');
            }

            // Validasi ulang status tenant
            if ($application->tenant->activeContract()) {
                throw new InvalidArgumentException('Penghuni sudah memiliki kontrak aktif lain.');
            }

            $application->update([
                'status' => StatusApplication::Approved,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
            ]);

            return $application;
        });
    }

    /**
     * Menolak pengajuan sewa oleh staf (Admin/Owner).
     */
    public function reject(TenantApplication $application, int $reviewerId, string $rejectionReason): TenantApplication
    {
        return DB::transaction(function () use ($application, $reviewerId, $rejectionReason) {
            if ($application->status !== StatusApplication::Pending) {
                throw new InvalidArgumentException('Hanya pengajuan berstatus pending yang dapat ditolak.');
            }

            if (empty(trim($rejectionReason))) {
                throw new InvalidArgumentException('Alasan penolakan wajib diisi.');
            }

            $application->update([
                'status' => StatusApplication::Rejected,
                'rejection_reason' => $rejectionReason,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
            ]);

            return $application;
        });
    }

    /**
     * Membatalkan pengajuan sewa oleh tenant pemilik.
     */
    public function cancel(TenantApplication $application, int $tenantId): TenantApplication
    {
        return DB::transaction(function () use ($application, $tenantId) {
            if ($application->tenant_id !== $tenantId) {
                throw new InvalidArgumentException('Anda tidak memiliki hak untuk membatalkan pengajuan ini.');
            }

            if ($application->status !== StatusApplication::Pending) {
                throw new InvalidArgumentException('Hanya pengajuan berstatus pending yang dapat dibatalkan.');
            }

            $application->update([
                'status' => StatusApplication::Cancelled,
            ]);

            return $application;
        });
    }
}
