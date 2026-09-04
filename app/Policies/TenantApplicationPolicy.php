<?php

namespace App\Policies;

use App\Enums\StatusApplication;
use App\Models\TenantApplication;
use App\Models\User;

class TenantApplicationPolicy
{
    /**
     * Staf (Owner/Admin) dapat melihat daftar seluruh pengajuan kamar.
     */
    public function viewAny(User $user): bool
    {
        return $user->isOwner() || $user->isAdmin();
    }

    /**
     * Staf dapat melihat semua detail pengajuan; Tenant hanya dapat melihat miliknya sendiri.
     */
    public function view(User $user, TenantApplication $application): bool
    {
        if ($user->isOwner() || $user->isAdmin()) {
            return true;
        }

        return $user->isTenant()
            && $user->tenant !== null
            && $application->tenant_id === $user->tenant->id;
    }

    /**
     * Hanya calon tenant tanpa kontrak aktif dan tanpa pending application yang boleh mengajukan.
     */
    public function create(User $user): bool
    {
        if (! $user->isTenant() || ! $user->tenant) {
            return false;
        }

        // Tidak boleh mengajukan jika sudah punya kontrak aktif
        if ($user->tenant->activeContract()) {
            return false;
        }

        // Tidak boleh mengajukan jika masih punya pengajuan pending
        if ($user->tenant->pendingApplication()) {
            return false;
        }

        return true;
    }

    /**
     * Hanya Staf yang boleh menyetujui pengajuan berstatus pending.
     */
    public function approve(User $user, TenantApplication $application): bool
    {
        return ($user->isOwner() || $user->isAdmin())
            && $application->status === StatusApplication::Pending;
    }

    /**
     * Hanya Staf yang boleh menolak pengajuan berstatus pending.
     */
    public function reject(User $user, TenantApplication $application): bool
    {
        return ($user->isOwner() || $user->isAdmin())
            && $application->status === StatusApplication::Pending;
    }

    /**
     * Tenant pemilik boleh membatalkan pengajuannya sendiri jika masih pending.
     */
    public function cancel(User $user, TenantApplication $application): bool
    {
        return $user->isTenant()
            && $user->tenant !== null
            && $application->tenant_id === $user->tenant->id
            && $application->status === StatusApplication::Pending;
    }
}
