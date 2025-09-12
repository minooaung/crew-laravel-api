<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UserService
{
    private const MAX_ADMIN_USERS = 5;

    /**
     * Create a new user
     *
     * @param array $data User data including name, email, password, and role
     * @throws InvalidArgumentException When admin limit is reached
     * @return User
     */
    public function create(array $data): User
    {
        // Set default role if not provided
        $data['role'] = $data['role'] ?? 'EMPLOYEE';

        // Check admin limit
        if ($data['role'] === 'ADMIN' && !$this->canCreateAdmin()) {
            throw new InvalidArgumentException(
                'Cannot create ADMIN user. System already has maximum limit of ' . self::MAX_ADMIN_USERS . ' Admin Users.'
            );
        }

        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Create user
        return User::create($data);
    }

    /**
     * Check if a new admin user can be created
     *
     * @return bool
     */
    public function canCreateAdmin(): bool
    {
        return User::where('role', 'ADMIN')->count() < self::MAX_ADMIN_USERS;
    }

    /**
     * Update an existing user
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        // Hash password if it's being updated
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Check admin limit if role is being changed to ADMIN
        if (
            isset($data['role']) &&
            $data['role'] === 'ADMIN' &&
            $user->role !== 'ADMIN' &&
            !$this->canCreateAdmin()
        ) {
            throw new InvalidArgumentException(
                'Cannot change role to ADMIN. System already has maximum limit of ' . self::MAX_ADMIN_USERS . ' Admin Users.'
            );
        }

        $user->update($data);
        return $user;
    }

    /**
     * Delete a user if they have no associated records
     *
     * @param User $user
     * @return bool
     * @throws InvalidArgumentException When user has associated records
     */
    public function delete(User $user): bool
    {
        if ($user->organisationUsers()->count() > 0) {
            throw new InvalidArgumentException('User cannot be deleted due to existing relationships with organisations.');
        }

        return $user->delete();
    }
}
