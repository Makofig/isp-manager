<?php

namespace App\Policies;

use App\Models\Access_Point;
use App\Models\User;

class AccessPointPolicy
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
    public function view(User $user, Access_Point $accessPoint): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_OPERATOR, User::ROLE_AUDITOR]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_OPERATOR]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Access_Point $accessPoint): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_OPERATOR]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Access_Point $accessPoint): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
