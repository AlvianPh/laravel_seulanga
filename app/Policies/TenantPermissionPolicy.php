<?php

namespace App\Policies;

use App\Enums\RoleUser;
use App\Models\TenantPermission;
use App\Models\User;

class TenantPermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TenantPermission $permission): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null && $user->tenant->id === $permission->tenant_id;
        }

        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function create(User $user): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null && $user->tenant->activeContract() !== null;
        }

        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function cancel(User $user, TenantPermission $permission): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null
                && $user->tenant->id === $permission->tenant_id
                && $permission->isPending();
        }

        return false;
    }

    public function approve(User $user, TenantPermission $permission): bool
    {
        if (! in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true)) {
            return false;
        }

        return $permission->isPending();
    }

    public function reject(User $user, TenantPermission $permission): bool
    {
        if (! in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true)) {
            return false;
        }

        return $permission->isPending();
    }

    public function delete(User $user, TenantPermission $permission): bool
    {
        return $user->role === RoleUser::Owner;
    }
}
