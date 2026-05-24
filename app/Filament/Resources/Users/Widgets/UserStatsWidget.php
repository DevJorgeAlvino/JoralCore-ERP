<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsers = $totalUsers - $verifiedUsers;

        return [
            Stat::make('Total Usuarios', $totalUsers)
                ->description('Usuarios registrados en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Cuentas Verificadas', $verifiedUsers)
                ->description('Correos confirmados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Pendientes', $unverifiedUsers)
                ->description('Correos sin confirmar')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
        ];
    }
}
