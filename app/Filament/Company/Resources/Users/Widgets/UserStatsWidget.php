<?php

namespace App\Filament\Company\Resources\Users\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        
        // Asumiendo que el modelo Company tiene la relación ->users()
        $totalUsers = $tenant->users()->count();
        $verifiedUsers = $tenant->users()->whereNotNull('email_verified_at')->count();
        $unverifiedUsers = $totalUsers - $verifiedUsers;

        return [
            Stat::make(__('users.widgets.stats.company_total'), $totalUsers)
                ->description(__('users.widgets.stats.company_total_desc'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make(__('users.widgets.stats.verified'), $verifiedUsers)
                ->description(__('users.widgets.stats.verified_desc'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make(__('users.widgets.stats.pending'), $unverifiedUsers)
                ->description(__('users.widgets.stats.pending_desc'))
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
        ];
    }
}
