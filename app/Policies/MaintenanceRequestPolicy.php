<?php

namespace App\Policies;

use App\Enums\RoleUser;
use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenanceRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null && $user->tenant->id === $maintenanceRequest->tenant_id;
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

    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function updateStatus(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        return $user->role === RoleUser::Owner;
    }
}
