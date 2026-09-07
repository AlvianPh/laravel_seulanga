<?php

namespace App\Services;

use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use App\Models\Tenant;
use App\Models\TenantPermission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * TenantPermissionService
 *
 * Menangani siklus hidup permohonan izin oleh penghuni kost:
 *  - Pembuatan izin oleh penghuni dengan kontrak aktif
 *  - Pembatalan izin oleh penghuni sendiri
 *  - Persetujuan (Approve) izin oleh staf (Admin/Owner)
 *  - Penolakan (Reject) izin oleh staf (Admin/Owner)
 */
class TenantPermissionService
{
    /**
     * Membuat permohonan izin baru oleh penghuni.
     */
    public function create(Tenant $tenant, array $data, User $actor): TenantPermission
    {
        return DB::transaction(function () use ($tenant, $data) {
            $activeContract = $tenant->activeContract();

            if (! $activeContract) {
                throw new InvalidArgumentException('Hanya penghuni dengan kontrak kamar aktif yang dapat mengajukan permohonan izin.');
            }

            $type = $data['type'] instanceof PermissionType
                ? $data['type']
                : PermissionType::from($data['type']);

            $startAt = ! empty($data['start_at']) ? $data['start_at'] : null;
            $endAt = ! empty($data['end_at']) ? $data['end_at'] : null;

            // Validasi urutan tanggal jika kedua tanggal ada
            if ($startAt && $endAt && strtotime($endAt) <= strtotime($startAt)) {
                throw new InvalidArgumentException('Waktu selesai harus lebih besar daripada waktu mulai.');
            }

            // Cegah duplikasi permohonan pending yang identik
            $duplicateQuery = TenantPermission::where('tenant_id', $tenant->id)
                ->where('type', $type->value)
                ->where('status', PermissionStatus::Pending);

            if ($startAt) {
                $duplicateQuery->where('start_at', $startAt);
            }
            if ($endAt) {
                $duplicateQuery->where('end_at', $endAt);
            }

            if ($duplicateQuery->exists()) {
                throw new InvalidArgumentException('Anda sudah memiliki permohonan izin sejenis yang sedang menunggu persetujuan pada periode tersebut.');
            }

            return TenantPermission::create([
                'tenant_id' => $tenant->id,
                'contract_id' => $activeContract->id,
                'type' => $type,
                'title' => $data['title'],
                'description' => $data['description'],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => PermissionStatus::Pending,
            ]);
        });
    }

    /**
     * Membatalkan permohonan izin oleh penghuni.
     */
    public function cancel(TenantPermission $permission, User $actor): TenantPermission
    {
        return DB::transaction(function () use ($permission) {
            if (! $permission->isPending()) {
                throw new InvalidArgumentException('Hanya permohonan izin berstatus menunggu yang dapat dibatalkan.');
            }

            $permission->update([
                'status' => PermissionStatus::Cancelled,
            ]);

            return $permission->fresh(['tenant', 'contract.room', 'reviewer']);
        });
    }

    /**
     * Menyetujui permohonan izin oleh staf (Admin/Owner).
     */
    public function approve(TenantPermission $permission, ?string $note, User $actor): TenantPermission
    {
        return DB::transaction(function () use ($permission, $note, $actor) {
            if (! $permission->isPending()) {
                throw new InvalidArgumentException('Permohonan izin ini sudah diproses dan tidak dapat disetujui lagi.');
            }

            $permission->update([
                'status' => PermissionStatus::Approved,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'review_note' => $note,
            ]);

            return $permission->fresh(['tenant', 'contract.room', 'reviewer']);
        });
    }

    /**
     * Menolak permohonan izin oleh staf (Admin/Owner).
     */
    public function reject(TenantPermission $permission, string $note, User $actor): TenantPermission
    {
        return DB::transaction(function () use ($permission, $note, $actor) {
            if (! $permission->isPending()) {
                throw new InvalidArgumentException('Permohonan izin ini sudah diproses dan tidak dapat ditolak lagi.');
            }

            if (trim($note) === '') {
                throw new InvalidArgumentException('Alasan penolakan izin wajib diisi.');
            }

            $permission->update([
                'status' => PermissionStatus::Rejected,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'review_note' => $note,
            ]);

            return $permission->fresh(['tenant', 'contract.room', 'reviewer']);
        });
    }
}
