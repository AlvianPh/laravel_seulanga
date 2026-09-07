<?php

namespace App\Policies;

use App\Enums\DocumentStatus;
use App\Enums\RoleUser;
use App\Models\TenantDocument;
use App\Models\User;

class TenantDocumentPolicy
{
    /**
     * Menentukan apakah user dapat melihat daftar dokumen.
     */
    public function viewAny(User $user): bool
    {
        if ($user->role === RoleUser::Owner || $user->role === RoleUser::Admin) {
            return true;
        }

        return $user->tenant !== null;
    }

    /**
     * Menentukan apakah user dapat melihat dokumen tertentu.
     */
    public function view(User $user, TenantDocument $document): bool
    {
        if ($user->role === RoleUser::Owner || $user->role === RoleUser::Admin) {
            return true;
        }

        return $user->tenant && $user->tenant->id === $document->tenant_id;
    }

    /**
     * Menentukan apakah user dapat mengunggah dokumen baru.
     */
    public function create(User $user): bool
    {
        if ($user->role === RoleUser::Owner || $user->role === RoleUser::Admin) {
            return true;
        }

        return $user->tenant !== null;
    }

    /**
     * Menentukan apakah user dapat mengunduh / membuka berkas dokumen.
     */
    public function download(User $user, TenantDocument $document): bool
    {
        return $this->view($user, $document);
    }

    /**
     * Menentukan apakah user dapat memverifikasi atau menolak dokumen.
     */
    public function verify(User $user, TenantDocument $document): bool
    {
        return $user->role === RoleUser::Owner || $user->role === RoleUser::Admin;
    }

    /**
     * Menentukan apakah user dapat menghapus dokumen.
     */
    public function delete(User $user, TenantDocument $document): bool
    {
        if ($user->role === RoleUser::Owner || $user->role === RoleUser::Admin) {
            return true;
        }

        // Tenant hanya boleh menghapus dokumen miliknya sendiri jika statusnya masih pending
        return $user->tenant
            && $user->tenant->id === $document->tenant_id
            && $document->status === DocumentStatus::Pending;
    }
}
