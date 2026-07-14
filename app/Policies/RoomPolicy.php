<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

/**
 * Policy RoomPolicy — mengatur otorisasi manajemen kamar.
 *
 * Sesuai kebutuhan, modul operasional (seperti kamar)
 * dapat diakses oleh Owner maupun Admin secara bebas.
 * Karena di web.php route 'rooms' sudah dilindungi middleware 'auth',
 * semua method policy ini bisa sekadar me-return true (selama user login).
 */
class RoomPolicy
{
    /**
     * Melihat daftar kamar — Owner & Admin.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Melihat detail kamar — Owner & Admin.
     */
    public function view(User $user, Room $room): bool
    {
        return true;
    }

    /**
     * Membuat data kamar baru — Owner & Admin.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Mengubah data kamar — Owner & Admin.
     */
    public function update(User $user, Room $room): bool
    {
        return true;
    }

    /**
     * Menghapus data kamar — Owner & Admin.
     */
    public function delete(User $user, Room $room): bool
    {
        return true;
    }
}
