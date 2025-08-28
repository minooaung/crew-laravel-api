<?php

namespace App\Services;

use App\Models\User;
use App\Models\Organisation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get all dashboard statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'stats' => $this->getBasicStats(),
            'userRoles' => $this->getUserRoleStats(),
            'growth' => $this->getGrowthStats()
        ];
    }

    /**
     * Get basic statistics
     *
     * @return array
     */
    protected function getBasicStats(): array
    {
        $activeOrganizations = Organisation::has('users')->count();

        return [
            'totalUsers' => User::count(),
            'totalOrganizations' => Organisation::count(),
            'adminUsers' => User::where('role', 'ADMIN')->count(),
            'activeOrganizations' => $activeOrganizations
        ];
    }

    /**
     * Get user role distribution statistics
     *
     * @return array
     */
    protected function getUserRoleStats(): array
    {
        return [
            'adminCount' => User::where('role', 'ADMIN')->count(),
            'employeeCount' => User::where('role', 'EMPLOYEE')->count()
        ];
    }

    /**
     * Get growth statistics for the past 6 months
     *
     * @return array
     */
    protected function getGrowthStats(): array
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        $monthlyUserData = $this->getMonthlyData('users', $sixMonthsAgo);
        $monthlyOrgData = $this->getMonthlyData('organisations', $sixMonthsAgo);

        return [
            'labels' => $monthlyUserData->pluck('month')->toArray(),
            'users' => $monthlyUserData->pluck('count')->toArray(),
            'organizations' => $monthlyOrgData->pluck('count')->toArray()
        ];
    }

    /**
     * Get monthly data for a specific table
     *
     * @param string $table
     * @param Carbon $startDate
     * @return Collection
     */
    protected function getMonthlyData(string $table, Carbon $startDate): Collection
    {
        return DB::table($table)
            ->select([
                DB::raw('DATE_FORMAT(created_at, "%b") as month'),
                DB::raw('COUNT(*) as count')
            ])
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('created_at')
            ->get();
    }
} 