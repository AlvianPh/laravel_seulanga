<?php

namespace App\Policies;

use App\Enums\RoleUser;
use App\Models\MoveOutRequest;
use App\Models\User;

class MoveOutRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MoveOutRequest $request): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null && $user->tenant->id === $request->tenant_id;
        }

        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function create(User $user): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null && $user->tenant->activeContract() !== null;
        }

        return false;
    }

    public function cancel(User $user, MoveOutRequest $request): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null
                && $user->tenant->id === $request->tenant_id
                && $request->isPending();
        }

        return false;
    }

    public function review(User $user, MoveOutRequest $request): bool
    {
        if (! in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true)) {
            return false;
        }

        return $request->isPending();
    }

    public function approve(User $user, MoveOutRequest $request): bool
    {
        if (! in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true)) {
            return false;
        }

        return $request->isPending();
    }

    public function reject(User $user, MoveOutRequest $request): bool
    {
        if (! in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true)) {
            return false;
        }

        return $request->isPending();
    }

    public function inspect(User $user, MoveOutRequest $request): bool
    {
        if (! in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true)) {
            return false;
        }

        return $request->canBeInspected();
    }

    public function finalize(User $user, MoveOutRequest $request): bool
    {
        if (! in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true)) {
            return false;
        }

        return $request->canBeFinalized() && ! $request->isCompleted();
    }

    public function delete(User $user, MoveOutRequest $request): bool
    {
        return $user->role === RoleUser::Owner;
    }
}
