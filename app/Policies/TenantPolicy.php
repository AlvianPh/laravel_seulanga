<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

/**
 * Policy TenantPolicy — mengatur otorisasi manajemen penghuni.
 * 
 * Owner dan Admin memiliki akses CRUD penuh ke penghuni.
 */
class TenantPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return true;
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return true;
    }
}
