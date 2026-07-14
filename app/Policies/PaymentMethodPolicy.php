<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    /**
     * Menentukan apakah user (Owner/Admin) bisa mengakses menu metode pembayaran.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }
}
