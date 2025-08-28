<?php

namespace App\Traits;

trait HasFormattedTimestamps
{
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at?->format('Y-m-d H:i') ?? '';
    }

    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at?->format('Y-m-d H:i') ?? '';
    }    
}
