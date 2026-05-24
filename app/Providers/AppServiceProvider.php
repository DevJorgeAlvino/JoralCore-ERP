<?php

namespace App\Providers;

use App\Models\Category;
use App\Policies\CategoryPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Gate::policy(Category::class, CategoryPolicy::class);

        Gate::before(function ($user, $ability) {
            
            // Si el usuario no tiene ID, salimos
            if (!$user || !$user->id) return null;

            // Consulta SQL directa para ignorar Spatie Tenants
            $isSuperAdmin = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id) // Tu ID de usuario
                ->where('roles.name', 'super_admin')           // El nombre exacto del rol
                ->exists();

            // Si es true, ABRE TODAS LAS PUERTAS
            return $isSuperAdmin ? true : null;
        });

        \BezhanSalleh\LanguageSwitch\LanguageSwitch::configureUsing(function (\BezhanSalleh\LanguageSwitch\LanguageSwitch $switch) {
            $switch
                ->locales(['es', 'en']); // Renderiza idiomas con sus banderas por defecto
        });
        
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
