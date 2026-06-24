<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        if ($tenant) {
            $totalUsers = $tenant->users()->count();
            $verifiedUsers = $tenant->users()->whereNotNull('email_verified_at')->count();
            $title = __('users.widgets.stats.company_total');
            $desc = __('users.widgets.stats.company_total_desc');
            $icon = 'heroicon-m-user-group';
        } else {
            $totalUsers = User::count();
            $verifiedUsers = User::whereNotNull('email_verified_at')->count();
            $title = __('users.widgets.stats.admin_total');
            $desc = __('users.widgets.stats.admin_total_desc');
            $icon = 'heroicon-m-users';
        }

        $unverifiedUsers = $totalUsers - $verifiedUsers;

        return [
            Stat::make($title, $totalUsers)
                ->description($desc)
                ->descriptionIcon($icon)
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
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
