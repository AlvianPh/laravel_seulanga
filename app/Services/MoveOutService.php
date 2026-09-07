<?php

namespace App\Services;

use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusMoveOut;
use App\Enums\StatusTagihan;
use App\Models\MoveOutRequest;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service untuk menangani siklus hidup Move-Out dan pengakhiran kontrak sewa.
 */
class MoveOutService
{
    /**
     * Membuat permohonan move-out baru oleh penghuni.
     */
    public function createRequest(Tenant $tenant, array $data, User $actor): MoveOutRequest
    {
        return DB::transaction(function () use ($tenant, $data) {
            $activeContract = $tenant->activeContract();

            if (! $activeContract) {
                throw new InvalidArgumentException('Hanya penghuni dengan kontrak sewa aktif yang dapat mengajukan move-out.');
            }

            if ($activeContract->activeMoveOutRequest()) {
                throw new InvalidArgumentException('Anda sudah memiliki permohonan move-out yang sedang diproses untuk kontrak ini.');
            }

            $moveOutDate = Carbon::parse($data['requested_move_out_date'])->startOfDay();
            if ($moveOutDate->isBefore(today())) {
                throw new InvalidArgumentException('Tanggal rencana keluar kost tidak boleh berada di masa lalu.');
            }

            return MoveOutRequest::create([
                'tenant_id' => $tenant->id,
                'contract_id' => $activeContract->id,
                'room_id' => $activeContract->room_id,
                'requested_move_out_date' => $moveOutDate->format('Y-m-d'),
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'status' => StatusMoveOut::Pending,
            ]);
        });
    }

    /**
     * Membatalkan permohonan move-out oleh penghuni sendiri.
     */
    public function cancel(MoveOutRequest $request, User $actor): MoveOutRequest
    {
        return DB::transaction(function () use ($request) {
            if (! $request->isPending()) {
                throw new InvalidArgumentException('Hanya permohonan move-out berstatus menunggu yang dapat dibatalkan.');
            }

            $request->update([
                'status' => StatusMoveOut::Cancelled,
            ]);

            return $request->fresh(['tenant', 'contract.room', 'reviewer']);
        });
    }

    /**
     * Menyetujui permohonan move-out oleh staf (Admin/Owner).
     */
    public function approve(MoveOutRequest $request, ?string $reviewNote, User $actor): MoveOutRequest
    {
        return DB::transaction(function () use ($request, $reviewNote, $actor) {
            if (! $request->isPending()) {
                throw new InvalidArgumentException('Permohonan move-out ini sudah diproses dan tidak dapat disetujui lagi.');
            }

            $request->update([
                'status' => StatusMoveOut::Approved,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ]);

            return $request->fresh(['tenant', 'contract.room', 'reviewer']);
        });
    }

    /**
     * Menolak permohonan move-out oleh staf (Admin/Owner).
     */
    public function reject(MoveOutRequest $request, string $reviewNote, User $actor): MoveOutRequest
    {
        return DB::transaction(function () use ($request, $reviewNote, $actor) {
            if (! $request->isPending()) {
                throw new InvalidArgumentException('Permohonan move-out ini sudah diproses dan tidak dapat ditolak lagi.');
            }

            if (trim($reviewNote) === '') {
                throw new InvalidArgumentException('Alasan penolakan move-out wajib diisi.');
            }

            $request->update([
                'status' => StatusMoveOut::Rejected,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ]);

            return $request->fresh(['tenant', 'contract.room', 'reviewer']);
        });
    }

    /**
     * Mencatat hasil inspeksi fisik kamar oleh staf.
     */
    public function recordInspection(MoveOutRequest $request, array $data, User $actor): MoveOutRequest
    {
        return DB::transaction(function () use ($request, $data, $actor) {
            if (! $request->canBeInspected()) {
                throw new InvalidArgumentException('Inspeksi hanya dapat dilakukan pada permohonan yang telah disetujui.');
            }

            $damageCost = isset($data['damage_cost']) ? max(0.0, (float) $data['damage_cost']) : 0.0;
            $requiresMaintenance = ! empty($data['requires_room_maintenance']);

            $request->update([
                'status' => StatusMoveOut::Inspection,
                'inspected_by' => $actor->id,
                'inspected_at' => now(),
                'room_condition' => $data['room_condition'] ?? 'baik',
                'damage_notes' => $data['damage_notes'] ?? null,
                'damage_cost' => $damageCost,
                'requires_room_maintenance' => $requiresMaintenance,
                'inspection_notes' => $data['inspection_notes'] ?? null,
            ]);

            return $request->fresh(['tenant', 'contract.room', 'inspector']);
        });
    }

    /**
     * Menghitung rincian finansial settlement berdasarkan data server-side.
     */
    public function calculateSettlement(MoveOutRequest $request): array
    {
        $contract = $request->contract;
        $depositAmount = (float) ($contract->deposit_amount ?? 0);

        // Hitung total sisa tagihan belum lunas dari seluruh invoice kontrak yang tidak dibatalkan
        $outstandingInvoices = $contract->invoices()
            ->where('status', '!=', StatusTagihan::Cancelled)
            ->get();

        $outstandingInvoicesAmount = 0.0;
        foreach ($outstandingInvoices as $invoice) {
            $outstandingInvoicesAmount += (float) $invoice->remainingBalance();
        }

        $damageCost = (float) ($request->damage_cost ?? 0);
        $totalObligations = $outstandingInvoicesAmount + $damageCost;

        $depositReturnedAmount = max(0.0, $depositAmount - $totalObligations);
        $remainingTenantLiability = max(0.0, $totalObligations - $depositAmount);

        return [
            'deposit_amount' => $depositAmount,
            'outstanding_invoices_amount' => $outstandingInvoicesAmount,
            'damage_deduction_amount' => $damageCost,
            'total_obligations' => $totalObligations,
            'deposit_returned_amount' => $depositReturnedAmount,
            'remaining_tenant_liability' => $remainingTenantLiability,
        ];
    }

    /**
     * Menyelesaikan seluruh proses settlement, mengakhiri kontrak, dan memperbarui status kamar.
     */
    public function finalize(MoveOutRequest $request, ?string $settlementNotes, User $actor): MoveOutRequest
    {
        return DB::transaction(function () use ($request, $settlementNotes, $actor) {
            if ($request->isCompleted()) {
                throw new InvalidArgumentException('Proses move-out ini sudah selesai sebelumnya.');
            }

            if (! $request->canBeFinalized()) {
                throw new InvalidArgumentException('Permohonan move-out belum siap untuk diselesaikan (harus melalui persetujuan & inspeksi).');
            }

            $calc = $this->calculateSettlement($request);

            // 1. Simpan settlement dan tandai move-out completed
            $request->update([
                'status' => StatusMoveOut::Completed,
                'settled_by' => $actor->id,
                'settled_at' => now(),
                'deposit_amount' => $calc['deposit_amount'],
                'outstanding_invoices_amount' => $calc['outstanding_invoices_amount'],
                'damage_deduction_amount' => $calc['damage_deduction_amount'],
                'deposit_returned_amount' => $calc['deposit_returned_amount'],
                'remaining_tenant_liability' => $calc['remaining_tenant_liability'],
                'settlement_notes' => $settlementNotes,
                'completed_by' => $actor->id,
                'completed_at' => now(),
            ]);

            // 2. Akhiri kontrak (status = ended)
            $contract = $request->contract;
            $contract->update([
                'status' => StatusKontrak::Ended,
            ]);

            // 3. Sinkronisasi status kamar
            $room = $request->room;
            if ($room) {
                if ($request->requires_room_maintenance) {
                    $room->update(['status' => StatusKamar::Maintenance]);
                } else {
                    $room->update(['status' => StatusKamar::Available]);
                }
            }

            return $request->fresh(['tenant', 'contract', 'room', 'reviewer', 'inspector', 'settler', 'completer']);
        });
    }
}
