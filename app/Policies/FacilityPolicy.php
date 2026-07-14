<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

/**
 * Policy FacilityPolicy — mengatur otorisasi manajemen fasilitas.
 * Owner dan Admin keduanya dapat mengakses modul ini.
 */
class FacilityPolicy
{
    /** Melihat daftar fasilitas — Owner & Admin. */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Melihat detail fasilitas — Owner & Admin. */
    public function view(User $user, Facility $facility): bool
    {
        return true;
    }

    /** Membuat fasilitas baru — Owner & Admin. */
    public function create(User $user): bool
    {
        return true;
    }

    /** Mengubah fasilitas — Owner & Admin. */
    public function update(User $user, Facility $facility): bool
    {
        return true;
    }

    /** Menghapus fasilitas — Owner & Admin (dicek di Controller apakah masih dipakai). */
    public function delete(User $user, Facility $facility): bool
    {
        return true;
    }
}
