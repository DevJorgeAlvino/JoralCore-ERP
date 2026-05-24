<?php

namespace App\Providers\Filament;

use Filament\Facades\Filament;
use App\Filament\Pages\Tenancy\EditCompanyProfile;
use App\Http\Middleware\ApplyTenantBranding;
use App\Http\Middleware\SetUserCompanyTenant;
use App\Models\Company;
use App\Services\CompanySettingService;
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

                        if (!$logoPath) {
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
                        
                        if (!$faviconPath) {
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
                \App\Http\Middleware\CheckCompanyAccess::class,
            ])
            // Shield se gestiona solo desde el panel Admin (centralizado)
            // ->plugins([
            //     FilamentShieldPlugin::make(),
            // ])
            ->tenant(Company::class)
            ->tenantRegistration(\App\Filament\Pages\Tenancy\RegisterCompany::class)
            ->tenantProfile(EditCompanyProfile::class)
            ->tenantMiddleware([
                SetUserCompanyTenant::class,
                ApplyTenantBranding::class,
            ], isPersistent: true)
            ->navigationGroups([
                 NavigationGroup::make()
                 ->label('Sistema')
                 ->collapsed()
            ]);
    }

}
