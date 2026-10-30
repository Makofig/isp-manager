<?php

namespace App\Policies;

use App\Models\Payments;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_OPERATOR, User::ROLE_AUDITOR]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payments $payment): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_OPERATOR, User::ROLE_AUDITOR]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payments $payment): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_OPERATOR]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payments $payment): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
