<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    /**
     * Otorisasi melihat daftar semua kontrak (khusus Staff / Owner / Admin).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Otorisasi melihat detail kontrak.
     * Staff dapat melihat semua; Tenant HANYA dapat melihat kontrak miliknya sendiri.
     */
    public function view(User $user, Contract $contract): bool
    {
        if ($user->isAdmin() || $user->isOwner()) {
            return true;
        }

        if ($user->isTenant() && $user->tenant) {
            return $user->tenant->id === $contract->tenant_id;
        }

        return false;
    }

    /**
     * Otorisasi pembuatan kontrak baru (Staff only).
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Otorisasi update data kontrak (Staff only).
     */
    public function update(User $user, Contract $contract): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Otorisasi hapus kontrak (Staff only).
     */
    public function delete(User $user, Contract $contract): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Otorisasi aktivasi kontrak dari draft menjadi active (Staff only).
     * Tenant TIDAK BISA mengaktifkan kontrak sendiri.
     */
    public function activate(User $user, Contract $contract): bool
    {
        if (! ($user->isAdmin() || $user->isOwner())) {
            return false;
        }

        return $contract->isDraft();
    }

    /**
     * Otorisasi persetujuan tata tertib / digital agreement oleh tenant.
     */
    public function acceptAgreement(User $user, Contract $contract): bool
    {
        if (! $user->isTenant() || ! $user->tenant) {
            return false;
        }

        return $user->tenant->id === $contract->tenant_id
            && $contract->isDraft()
            && ! $contract->isAgreementAccepted();
    }
}
