<?php

namespace App\Policies;

use App\Models\RoomType;
use App\Models\User;

/**
 * Policy RoomTypePolicy — mengatur otorisasi manajemen tipe kamar.
 * Owner dan Admin keduanya dapat mengakses modul ini.
 */
class RoomTypePolicy
{
    /** Melihat daftar tipe kamar — Owner & Admin. */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Melihat detail tipe kamar — Owner & Admin. */
    public function view(User $user, RoomType $roomType): bool
    {
        return true;
    }

    /** Membuat tipe kamar baru — Owner & Admin. */
    public function create(User $user): bool
    {
        return true;
    }

    /** Mengubah tipe kamar — Owner & Admin. */
    public function update(User $user, RoomType $roomType): bool
    {
        return true;
    }

    /** Menghapus tipe kamar — Owner & Admin (dicek di Controller apakah masih dipakai). */
    public function delete(User $user, RoomType $roomType): bool
    {
        return true;
    }
}
