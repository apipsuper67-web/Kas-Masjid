<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Donation $donation): bool
    {
        return $user->hasAnyRole('admin', 'bendahara') || $donation->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Donation $donation): bool
    {
        return $user->hasAnyRole('admin');
    }

    public function delete(User $user, Donation $donation): bool
    {
        return $user->hasAnyRole('admin');
    }

    public function verify(User $user, Donation $donation): bool
    {
        return $user->hasAnyRole('admin') && $donation->status === 'pending';
    }
}
