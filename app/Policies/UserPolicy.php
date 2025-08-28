<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function create(User $authUser)
    {
        // Allow ADMIN to create users (RBAC check only)
        return $authUser->role === 'ADMIN';
    }

    public function update(User $authUser, User $targetUser)
    {
        // Allow users to update their own profiles
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        // Only allow ADMIN to update non-ADMIN users
        if ($authUser->role === 'ADMIN' && $targetUser->role !== 'ADMIN') {
            return true;
        }

        // Everything else is unauthorized
        return false;
    }

    public function delete(User $authUser, User $targetUser)
    {
        // This ensures that only an ADMIN can delete an EMPLOYEE, and not themselves or another ADMIN
        // This policy method checks if the authenticated user is an ADMIN, the target user is an EMPLOYEE, and they are not trying to delete themselves

        if ($authUser->id === $targetUser->id) {
            return false; // Prevent self-deletion
        }

        return $authUser->role === 'ADMIN' && $targetUser->role !== 'ADMIN';
    }
}

