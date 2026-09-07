<?php

namespace App\Services;

use App\Enums\StatusMaintenance;
use App\Models\Expense;
use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * MaintenanceRequestService
 *
 * Menangani siklus hidup permintaan perbaikan / pemeliharaan kamar:
 *  - Pembuatan tiket perbaikan oleh tenant aktif
 *  - Update status perbaikan oleh staf (Admin/Owner)
 *  - Pengaitan biaya pengeluaran (Expense) ke tiket perbaikan
 */
class MaintenanceRequestService
{
    /**
     * Generate nomor tiket perbaikan unik (format: MNT-YYYYMMDD-XXXX).
     */
    public function generateTicketNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = "MNT-{$today}-";

        $lastTicket = MaintenanceRequest::where('ticket_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('ticket_number');

        if ($lastTicket) {
            $lastSequence = (int) substr($lastTicket, -4);
            $newSequence = str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '0001';
        }

        return "{$prefix}{$newSequence}";
    }

    /**
     * Buat permintaan maintenance baru oleh tenant.
     */
    public function createRequest(Tenant $tenant, array $data, ?UploadedFile $photo, User $actor): MaintenanceRequest
    {
        return DB::transaction(function () use ($tenant, $data, $photo, $actor) {
            $activeContract = $tenant->activeContract();

            if (! $activeContract) {
                throw new InvalidArgumentException('Hanya penghuni dengan kontrak aktif yang dapat mengajukan perbaikan kamar.');
            }

            $photoPath = null;
            if ($photo) {
                $photoPath = $photo->store('maintenance_photos', 'public');
            }

            $ticketNumber = $this->generateTicketNumber();

            return MaintenanceRequest::create([
                'ticket_number' => $ticketNumber,
                'tenant_id' => $tenant->id,
                'room_id' => $activeContract->room_id,
                'category' => $data['category'],
                'priority' => $data['priority'],
                'status' => StatusMaintenance::Pending,
                'location' => $data['location'],
                'description' => $data['description'],
                'photo_path' => $photoPath,
                'reported_at' => now(),
                'created_by' => $actor->id,
            ]);
        });
    }

    /**
     * Perbarui status permintaan maintenance oleh staf.
     */
    public function updateStatus(
        MaintenanceRequest $request,
        StatusMaintenance $newStatus,
        array $additionalData,
        User $actor
    ): MaintenanceRequest {
        return DB::transaction(function () use ($request, $newStatus, $additionalData, $actor) {
            // Validasi transisi status
            if ($request->isResolved() || $request->isRejected()) {
                throw new InvalidArgumentException('Permintaan perbaikan yang sudah selesai atau ditolak tidak dapat diubah statusnya.');
            }

            $payload = [
                'status' => $newStatus,
                'updated_by' => $actor->id,
            ];

            if (isset($additionalData['notes'])) {
                $payload['notes'] = $additionalData['notes'];
            }

            if ($newStatus === StatusMaintenance::Resolved) {
                $payload['resolved_at'] = now();
            }

            if ($newStatus === StatusMaintenance::Rejected) {
                if (empty($additionalData['rejection_reason'])) {
                    throw new InvalidArgumentException('Alasan penolakan wajib diisi.');
                }
                $payload['rejection_reason'] = $additionalData['rejection_reason'];
            }

            $request->update($payload);

            return $request->fresh(['tenant', 'room', 'expenses', 'creator', 'updater']);
        });
    }

    /**
     * Mengaitkan expense ke maintenance request.
     */
    public function linkExpense(MaintenanceRequest $request, Expense $expense): void
    {
        $expense->update([
            'maintenance_request_id' => $request->id,
        ]);
    }
}
