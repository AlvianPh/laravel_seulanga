<?php

namespace App\Services;

use App\Enums\StatusApplication;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service untuk memanipulasi Contract, Onboarding Agreement, dan sinkronisasi status Room.
 */
class ContractService
{
    /**
     * Membuat kontrak baru (bisa langsung Active atau sebagai Draft).
     */
    public function createContract(array $data, int $creatorId, bool $asDraft = false): Contract
    {
        return DB::transaction(function () use ($data, $creatorId, $asDraft) {
            $room = Room::findOrFail($data['room_id']);
            $tenant = Tenant::findOrFail($data['tenant_id']);

            if ($room->status !== StatusKamar::Available) {
                throw new InvalidArgumentException('Kamar tidak tersedia (sedang occupied atau maintenance).');
            }

            if ($tenant->activeContract()) {
                throw new InvalidArgumentException('Penghuni ini sudah memiliki kontrak sewa aktif.');
            }

            $status = $asDraft ? StatusKontrak::Draft : StatusKontrak::Active;

            $contract = Contract::create([
                'tenant_id' => $data['tenant_id'],
                'room_id' => $data['room_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'rent_price' => $data['rent_price'],
                'deposit_amount' => $data['deposit_amount'],
                'status' => $status,
                'notes' => $data['notes'] ?? null,
                'created_by' => $creatorId,
                'application_id' => $data['application_id'] ?? null,
            ]);

            // Buat invoice awal untuk kontrak
            $this->createInitialInvoice($contract);

            // Jika status langsung active, ubah kamar jadi Occupied
            if ($status === StatusKontrak::Active) {
                $room->update(['status' => StatusKamar::Occupied]);
            }

            return $contract;
        });
    }

    /**
     * Membuat draft kontrak dari Tenant Application yang telah disetujui.
     */
    public function createDraftFromApplication(TenantApplication $application, array $data, int $creatorId): Contract
    {
        return DB::transaction(function () use ($application, $data, $creatorId) {
            if ($application->status !== StatusApplication::Approved) {
                throw new InvalidArgumentException('Hanya pengajuan yang telah disetujui (Approved) yang dapat dibuatkan kontrak.');
            }

            $room = $application->room;
            if ($room->status !== StatusKamar::Available) {
                throw new InvalidArgumentException('Kamar yang diajukan sudah tidak berstatus Available.');
            }

            $tenant = $application->tenant;
            if ($tenant->activeContract()) {
                throw new InvalidArgumentException('Penghuni sudah memiliki kontrak sewa aktif.');
            }

            $contractData = [
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'start_date' => $data['start_date'] ?? now()->format('Y-m-d'),
                'end_date' => $data['end_date'] ?? now()->addMonth()->format('Y-m-d'),
                'rent_price' => $data['rent_price'] ?? $room->monthly_price,
                'deposit_amount' => $data['deposit_amount'] ?? $room->deposit_price,
                'notes' => $data['notes'] ?? ($application->application_notes ? "Pengajuan #{$application->id}: {$application->application_notes}" : null),
                'application_id' => $application->id,
            ];

            return $this->createContract($contractData, $creatorId, asDraft: true);
        });
    }

    /**
     * Tenant menyetujui tata tertib / digital agreement.
     */
    public function acceptAgreement(Contract $contract, User $user): Contract
    {
        return DB::transaction(function () use ($contract, $user) {
            if ($contract->isAgreementAccepted()) {
                throw new InvalidArgumentException('Tata tertib kontrak ini sudah disetujui sebelumnya.');
            }

            $contract->update([
                'agreement_accepted_at' => now(),
                'agreement_accepted_by' => $user->id,
                'agreement_version' => 'v1.0',
            ]);

            return $contract;
        });
    }

    /**
     * Mengaktifkan contract draft setelah seluruh prasyarat onboarding terpenuhi.
     */
    public function activateContract(Contract $contract, int $actorId): Contract
    {
        return DB::transaction(function () use ($contract) {
            if (! $contract->isDraft()) {
                throw new InvalidArgumentException('Hanya kontrak berstatus Draft yang dapat diaktifkan.');
            }

            if (! $contract->isAgreementAccepted()) {
                throw new InvalidArgumentException('Tata tertib dan perjanjian sewa belum disetujui oleh penghuni.');
            }

            $room = $contract->room;
            if ($room->status !== StatusKamar::Available) {
                throw new InvalidArgumentException('Kamar tidak tersedia untuk diaktifkan (sedang occupied atau maintenance).');
            }

            $tenant = $contract->tenant;
            if ($tenant->activeContract()) {
                throw new InvalidArgumentException('Penghuni sudah memiliki kontrak sewa aktif lain.');
            }

            // Validasi verifikasi pembayaran awal
            if (! $contract->hasVerifiedInitialPayment()) {
                throw new InvalidArgumentException('Pembayaran awal / deposit belum diverifikasi oleh pengelola.');
            }

            // Aktivasi kontrak
            $contract->update(['status' => StatusKontrak::Active]);

            // Update status kamar jadi Occupied
            $room->update(['status' => StatusKamar::Occupied]);

            return $contract;
        });
    }

    /**
     * Memperpanjang kontrak: mengakhiri kontrak lama (ended)
     * dan membuat kontrak baru untuk periode berikutnya.
     */
    public function renewContract(Contract $oldContract, array $newData, int $creatorId): Contract
    {
        return DB::transaction(function () use ($oldContract, $newData, $creatorId) {
            if ($oldContract->status !== StatusKontrak::Active) {
                throw new InvalidArgumentException('Hanya kontrak yang aktif yang bisa diperpanjang.');
            }

            // Akhiri kontrak lama dengan damai (ended)
            $oldContract->update(['status' => StatusKontrak::Ended]);

            // Buat kontrak baru
            $newContract = Contract::create([
                'tenant_id' => $oldContract->tenant_id,
                'room_id' => $oldContract->room_id,
                'start_date' => $newData['start_date'],
                'end_date' => $newData['end_date'],
                'rent_price' => $newData['rent_price'],
                'deposit_amount' => $newData['deposit_amount'],
                'status' => StatusKontrak::Active,
                'notes' => $newData['notes'] ?? "Perpanjangan dari kontrak #{$oldContract->id}",
                'created_by' => $creatorId,
            ]);

            return $newContract;
        });
    }

    /**
     * Mengakhiri kontrak secara paksa/normal sebelum waktunya,
     * atau menandai selesai, dan membebaskan kamar.
     */
    public function terminateContract(Contract $contract, StatusKontrak $status = StatusKontrak::Terminated): void
    {
        DB::transaction(function () use ($contract, $status) {
            if ($contract->status !== StatusKontrak::Active && $contract->status !== StatusKontrak::Draft) {
                throw new InvalidArgumentException('Kontrak sudah tidak aktif.');
            }

            $contract->update(['status' => $status]);

            // Kembalikan status kamar jadi available jika sebelumnya occupied
            $room = $contract->room;
            if ($room && $room->status === StatusKamar::Occupied) {
                $room->update(['status' => StatusKamar::Available]);
            }
        });
    }

    /**
     * Helper membuat tagihan awal untuk kontrak baru/draft.
     */
    private function createInitialInvoice(Contract $contract): ?Invoice
    {
        $startDate = Carbon::parse($contract->start_date);

        return Invoice::create([
            'contract_id' => $contract->id,
            'tenant_id' => $contract->tenant_id,
            'room_id' => $contract->room_id,
            'year' => (int) $startDate->year,
            'month' => (int) $startDate->month,
            'rent_amount' => $contract->rent_price,
            'electricity_fee' => 0,
            'water_fee' => 0,
            'internet_fee' => 0,
            'penalty_fee' => 0,
            'other_fee' => 0,
            'total_amount' => $contract->rent_price,
            'due_date' => $startDate->format('Y-m-d'),
            'status' => StatusTagihan::Pending,
        ]);
    }
}
