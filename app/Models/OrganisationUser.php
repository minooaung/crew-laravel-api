<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganisationUser extends Pivot
{
    protected $table = 'organisation_user';

    protected $fillable = [
        'user_id',
        'organisation_id',
        'assigned_by',
    ];

    
    // OrganisationUser extends Pivot, not Model
    // Pivot models do not assume timestamps or incrementing by default.
    // Since your pivot table includes:
    // an id (primary key), and
    // created_at / updated_at columns,
    // I must explicitly tell Laravel
    public $timestamps = true; // Laravel handles created_at & updated_at timestamps
    public $incrementing = true; // Optional, only if you use 'id' as PK

    // Optional: Relationships
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
