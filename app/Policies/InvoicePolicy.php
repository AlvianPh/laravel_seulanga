<?php

namespace App\Policies;

use App\Enums\RoleUser;
use App\Enums\StatusTagihan;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->role === RoleUser::Tenant) {
            return $user->tenant !== null && $user->tenant->id === $invoice->tenant_id;
        }

        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return in_array($user->role, [RoleUser::Owner, RoleUser::Admin], true);
    }

    /**
     * Otorisasi pembayaran invoice oleh penghuni.
     */
    public function pay(User $user, Invoice $invoice): bool
    {
        if ($user->role !== RoleUser::Tenant) {
            return false;
        }

        if (! $user->tenant || $user->tenant->id !== $invoice->tenant_id) {
            return false;
        }

        return ! in_array($invoice->status, [StatusTagihan::Paid, StatusTagihan::Cancelled], true);
    }
}
