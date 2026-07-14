<?php

namespace App\Policies;

use App\Models\ExpenseCategory;
use App\Models\User;

class ExpenseCategoryPolicy
{
    /**
     * Menentukan apakah user (Owner/Admin) bisa mengakses menu kategori pengeluaran.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function view(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function delete(User $user, ExpenseCategory $expenseCategory): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }
}
