<?php

namespace App\Policies;

use App\Enums\RoleUser;
use App\Models\User;

/**
 * Policy UserPolicy — mengatur otorisasi manajemen akun user.
 *
 * Hanya Owner yang boleh membuat, melihat daftar, mengubah, atau menghapus
 * akun user lain. Admin sama sekali tidak punya akses ke modul ini.
 */
class UserPolicy
{
    /**
     * Melihat daftar semua user — hanya Owner.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === RoleUser::Owner;
    }

    /**
     * Melihat detail satu user — hanya Owner.
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === RoleUser::Owner;
    }

    /**
     * Membuat akun user baru — hanya Owner.
     */
    public function create(User $user): bool
    {
        return $user->role === RoleUser::Owner;
    }

    /**
     * Mengubah data user (termasuk mengubah role) — hanya Owner.
     * Owner tidak bisa mengubah data dirinya sendiri lewat menu ini
     * untuk mencegah Owner tidak sengaja mengubah rolenya sendiri.
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === RoleUser::Owner && $user->id !== $model->id;
    }

    /**
     * Menghapus akun user — hanya Owner, dan tidak bisa hapus dirinya sendiri.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === RoleUser::Owner && $user->id !== $model->id;
    }
}
