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
        return in_array($user->role, ['admin', 'operator', 'auditor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return in_array($user->role, ['admin', 'operator', 'auditor']);
    }

    /**
     * Determine whether the user can create models (generate quotas).
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'operator']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->role === 'admin';
    }
}
