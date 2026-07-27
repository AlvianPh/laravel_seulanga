<?php

namespace App\Policies;

use App\Models\AdditionalFeeType;
use App\Models\User;

class AdditionalFeeTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function view(User $user, AdditionalFeeType $additionalFeeType): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user, AdditionalFeeType $additionalFeeType): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function delete(User $user, AdditionalFeeType $additionalFeeType): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }
}
