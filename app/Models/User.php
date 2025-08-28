<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Traits\HasFormattedTimestamps;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasFormattedTimestamps;

    protected $appends = ['formatted_created_at', 'formatted_updated_at'];

    /**
     * Attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Ensure role is included in fillable attributes
    ];

    /**
     * Attributes that should be hidden in arrays or JSON.
     */
    protected $hidden = [
        'password'
    ];

    /**
     * Casts for attributes.
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Role check helpers (optional but useful)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'EMPLOYEE';
    }

    /**
     * Many-to-many relationship with organisations.
     */
    public function organisations()
    {
        return $this->belongsToMany(Organisation::class, 'organisation_user')
                    ->using(OrganisationUser::class)
                    ->withPivot(['assigned_by'])
                    ->withTimestamps();
    }

    public function organisationUsers()
    {
        return $this->hasMany(OrganisationUser::class, 'assigned_by'); // ✅ Define the relationship
    }
}