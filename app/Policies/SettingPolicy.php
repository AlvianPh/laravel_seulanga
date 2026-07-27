<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
{
    public function view(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }
}
