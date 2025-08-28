<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Organisation;

class OrganisationPolicy
{
    /**
     * Determine whether the user can view any organisations.
     */
    // public function viewAny(User $user): bool
    // {
    //     return $user->isAdmin() || $user->isEmployee();
    // }

    /**
     * Determine whether the user can view the organisation.
     */
    // public function view(User $user, Organisation $organisation): bool
    // {
    //     // Allowed if the organisation is assigned to the user via pivot table
    //     return $user->organisations()->where('organisation_id', $organisation->id)->exists();
    // }

    /**
     * Determine whether the user can create organisations.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the organisation.
     */
    public function update(User $user, Organisation $organisation): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the organisation.
     */
    public function delete(User $user, Organisation $organisation): bool
    {
        return $user->isAdmin();
    }
}