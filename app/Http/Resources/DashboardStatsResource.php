<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'stats' => [
                'totalUsers' => $this['stats']['totalUsers'],
                'totalOrganizations' => $this['stats']['totalOrganizations'],
                'adminUsers' => $this['stats']['adminUsers'],
                'activeOrganizations' => $this['stats']['activeOrganizations'],
            ],
            'userRoles' => [
                'adminCount' => $this['userRoles']['adminCount'],
                'employeeCount' => $this['userRoles']['employeeCount'],
            ],
            'growth' => [
                'labels' => $this['growth']['labels'],
                'users' => $this['growth']['users'],
                'organizations' => $this['growth']['organizations'],
            ],
            'meta' => [
                'lastUpdated' => now()->toIso8601String(),
            ],
        ];
    }
} 