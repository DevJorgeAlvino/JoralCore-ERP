<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Tenancy\EditCompanyProfile;
use App\Filament\Pages\Tenancy\RegisterCompany;
use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\UnitMeasures\UnitMeasureResource;
use App\Filament\Resources\Users\UserResource;
use App\Http\Middleware\ApplyTenantBranding;
use App\Http\Middleware\CheckCompanyAccess;
use App\Http\Middleware\SetUserCompanyTenant;
use App\Models\Company;
use App\Services\CompanySettingService;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CompanyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->id('company')
            ->path('company')
            ->login()
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
           // ─── Branding dinámico por tenant ────────────
            ->brandName(function () {
                try {
                    return Filament::getTenant()->name ?? 'JoralCore ERP';
                } catch (\Throwable $th) {
                    return 'JoralCore ERP';
                }
            })
            ->brandLogo(function () {

                try {
                    $tenant = filament()->getTenant();

                    if ($tenant) {

                        $logoPath = CompanySettingService::get($tenant->id, 'company_logo');

                        if (! $logoPath) {
                            return null;
                        }

                        $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';

                        return Storage::disk($disk)->url($logoPath);

                    }

                } catch (\Throwable) {
                    return null;
                }

            })
            ->favicon(function () {

                try {
                    $tenant = Filament::getTenant();

                    if ($tenant) {

                        $faviconPath = CompanySettingService::get($tenant->id, 'company_icon');

                        if (! $faviconPath) {
                            return null;
                        }

                        $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';

                        return Storage::disk($disk)->url($faviconPath);

                    }

                } catch (\Throwable) {
                    return null;
                }

            })
            ->discoverResources(in: app_path('Filament/Company/Resources'), for: 'App\Filament\Company\Resources')
            ->resources([
                RoleResource::class,
                UnitMeasureResource::class,
                UserResource::class,
                CategoryResource::class,
                BrandResource::class,
                ItemResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Company/Pages'), for: 'App\Filament\Company\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Company/Widgets'), for: 'App\Filament\Company\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckCompanyAccess::class,
            ])
           // Shield se gestiona solo desde el panel Admin (centralizado)
            ->plugins([
                FilamentShieldPlugin::make(),
                \CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin::make()
                    ->placeholder(__('Escribe para buscar...')),
            ])
            ->tenant(Company::class)
            ->tenantRegistration(RegisterCompany::class)
            ->tenantProfile(EditCompanyProfile::class)
            ->tenantMiddleware([
                SetUserCompanyTenant::class,
                ApplyTenantBranding::class,
            ], isPersistent: true)
            ->navigationGroups([
                NavigationGroup::make()->label('Catálogo'),
                NavigationGroup::make()->label('Sistema')->collapsed(),
            ]);
    }
}
