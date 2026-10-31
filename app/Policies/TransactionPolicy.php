<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole('admin', 'bendahara');
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->hasAnyRole('admin', 'bendahara');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole('admin', 'bendahara');
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->hasAnyRole('admin', 'bendahara') && ! $transaction->donation()->exists();
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->hasAnyRole('admin', 'bendahara') && ! $transaction->donation()->exists();
    }
}
