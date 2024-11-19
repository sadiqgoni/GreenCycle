<?php

namespace App\Filament\Management\Widgets\Stats;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected static ?string $pollingInterval = '10s'; 
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // Get counts for each user role
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $householdCount = User::where('role', 'household')->count();
        $companyCount = User::where('role', 'company')->count();

        return [
            Stat::make('Total System Users', $totalUsers)
                ->description('Currently active users in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Admins', $adminCount)
                ->description('Total number of Admins')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),

            Stat::make('Households', $householdCount)
                ->description('Total number of Household Users')
                ->descriptionIcon('heroicon-m-home')
                ->color('warning'),

            Stat::make('Companies', $companyCount)
                ->description('Total number of Companies')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('info'),
        ];
    }
}
