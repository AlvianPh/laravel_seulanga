<?php

namespace App\Policies;

use App\Enums\RoleUser;
use App\Enums\StatusPembayaran;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null && $user->tenant->id === $payment->tenant_id;
        }

        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Payment $payment): bool
    {
        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    /**
     * Owner dan Admin boleh memverifikasi pembayaran pada endpoint staff.
     */
    public function verify(User $user, Payment $payment): bool
    {
        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    /**
     * Otorisasi melihat/mencetak kuitansi pembayaran.
     * Kuitansi hanya tersedia jika pembayaran sudah diverifikasi.
     */
    public function receipt(User $user, Payment $payment): bool
    {
        if ($payment->status !== StatusPembayaran::Verified) {
            return false;
        }

        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null && $user->tenant->id === $payment->tenant_id;
        }

        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }
}
