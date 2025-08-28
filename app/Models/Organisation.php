<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrganisationUser;
use App\Traits\HasFormattedTimestamps;

class Organisation extends Model
{
    use HasFactory, HasFormattedTimestamps;

    protected $appends = ['formatted_created_at', 'formatted_updated_at'];

    /**
     * Attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
    ];   

    /**
     * Many-to-many relationship with users.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'organisation_user')
                    ->using(OrganisationUser::class)
                    ->withPivot(['assigned_by'])
                    ->withTimestamps();
    }
}
