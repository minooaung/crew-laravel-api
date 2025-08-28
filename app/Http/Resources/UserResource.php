<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    public static $wrap = false; // Turn Off wrapping data inside another data Eg. data.data

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            //'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y')
        ];
    }
}
