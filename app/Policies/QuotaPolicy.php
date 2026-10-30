<?php

namespace App\Policies;

use App\Models\User;

class QuotaPolicy
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
    public function view(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_OPERATOR, User::ROLE_AUDITOR]);
    }

    /**
     * Determine whether the user can create models (generate quotas).
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_OPERATOR]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
