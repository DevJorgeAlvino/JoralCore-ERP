<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Tenancy\EditCompanyProfile;
use App\Filament\Pages\Tenancy\RegisterCompany;
use App\Http\Middleware\SetUserSuperAdmin;
use App\Models\Company;
use App\Services\SystemSettingService;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\Storage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        // dd($this->getBrandLogo());

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications(
                    livewireComponent: \App\Livewire\Notifications\AdminDatabaseNotifications::class
                )
            ->brandName($this->getBrandName())
            ->brandLogo($this->getBrandLogo())
            ->favicon($this->getFavicon())
            ->colors([
                'primary' => $this->getPrimaryColor(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
                SetUserSuperAdmin::class
            ])
            ->navigationGroups([
                 NavigationGroup::make()
                 ->label('Sistema')
                 ->collapsed()
            ]);
            // ->tenant(Company::class)
            // ->tenantRegistration(RegisterCompany::class)
            // ->tenantProfile(EditCompanyProfile::class);
    }

    // ─── Branding global desde system_settings ───────
    // Solo se aplica al panel Admin (login del ERP y panel del dueño)

    private function getBrandName(): string
    {
        try {
            return SystemSettingService::get('app_name', 'JoralERP');
        } catch (\Throwable) {
            return 'JoralERP';
        }
    }

    private function getBrandLogo(): ?string
    {
        try {
            $logoPath = SystemSettingService::get('app_logo');

            if (!$logoPath) {
                return null;
            }
    
            $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';

            return Storage::disk($disk)->url($logoPath);

        } catch (\Throwable) {
            return null;
        }
    }

    private function getFavicon(): ?string
    {
        try {
            $faviconPath = SystemSettingService::get('app_favicon');

            if (! $faviconPath) {
                return null;
            }

            $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';

            return Storage::disk($disk)->url($faviconPath);
            
        } catch (\Throwable) {
            return null;
        }
    }

    private function getPrimaryColor(): string|array
    {
        try {
            return SystemSettingService::get('color_primary', Color::Amber);
        } catch (\Throwable) {
            return Color::Amber;
        }
    }
}
