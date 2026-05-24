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
            Stat::make('Personal', $totalUsers)
                ->description('Empleados en la empresa')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Verificados', $verifiedUsers)
                ->description('Accesos confirmados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Pendientes', $unverifiedUsers)
                ->description('Falta confirmación')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
        ];
    }
}
