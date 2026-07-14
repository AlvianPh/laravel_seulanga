<?php

namespace App\Services;

use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Models\Contract;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service untuk memanipulasi Contract dan efek sampingnya ke status Room.
 */
class ContractService
{
    /**
     * Membuat kontrak baru dan mengubah status kamar menjadi Occupied.
     */
    public function createContract(array $data, int $creatorId): Contract
    {
        return DB::transaction(function () use ($data, $creatorId) {
            $room = Room::findOrFail($data['room_id']);

            if ($room->status !== StatusKamar::Available) {
                throw new InvalidArgumentException('Kamar tidak tersedia (sedang occupied atau maintenance).');
            }

            $contract = Contract::create([
                'tenant_id'      => $data['tenant_id'],
                'room_id'        => $data['room_id'],
                'start_date'     => $data['start_date'],
                'end_date'       => $data['end_date'],
                'rent_price'     => $data['rent_price'],
                'deposit_amount' => $data['deposit_amount'],
                'status'         => StatusKontrak::Active,
                'notes'          => $data['notes'] ?? null,
                'created_by'     => $creatorId,
            ]);

            // Ubah status kamar menjadi occupied
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

            // Kamar tetap di-set Occupied (karena penghuni lanjut)
            // tapi tidak perlu diubah, karena sudah Occupied dari kontrak sebelumnya.
            
            // Buat kontrak baru
            $newContract = Contract::create([
                'tenant_id'      => $oldContract->tenant_id,
                'room_id'        => $oldContract->room_id,
                'start_date'     => $newData['start_date'],
                'end_date'       => $newData['end_date'],
                'rent_price'     => $newData['rent_price'],
                'deposit_amount' => $newData['deposit_amount'], // Deposit mungkin ditambah/tetap
                'status'         => StatusKontrak::Active,
                'notes'          => $newData['notes'] ?? "Perpanjangan dari kontrak #{$oldContract->id}",
                'created_by'     => $creatorId,
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
            if ($contract->status !== StatusKontrak::Active) {
                throw new InvalidArgumentException('Kontrak sudah tidak aktif.');
            }

            $contract->update(['status' => $status]);

            // Kembalikan status kamar jadi available
            $room = $contract->room;
            if ($room && $room->status === StatusKamar::Occupied) {
                $room->update(['status' => StatusKamar::Available]);
            }
        });
    }
}
