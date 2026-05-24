<?php

namespace App\Http\Middleware;

use App\Services\CompanySettingService;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor; // ¡Esta es la clave!
use Illuminate\Support\Arr;

class ApplyTenantBranding
{
    /**
     * Aplica el branding dinámico del tenant activo al panel de Filament.
     * Este middleware DEBE ejecutarse DESPUÉS de SetUserCompanyTenant.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Aquí Filament ya resolvió el Tenant con total seguridad
        $tenant = filament()->getTenant();

        if ($tenant) {

            $primaryHex = CompanySettingService::get($tenant->id, 'primary_color', Color::Amber);
            $secondaryHex = CompanySettingService::get($tenant->id, 'secondary_color', Color::Gray);

            FilamentColor::register([
                'primary' => $primaryHex,
                'secondary' => $secondaryHex,
            ]);

        }

        return $next($request);

    }
}
