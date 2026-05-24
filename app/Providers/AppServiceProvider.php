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
                ->locales(['es', 'en'])
                ->flags([
                    'es' => 'https://flagcdn.com/w40/es.png',
                    'en' => 'https://flagcdn.com/w40/us.png',
                ]); 
        });

        // Asignar el company_id al momento de iniciar una importación para que se guarde en la BD
        \Illuminate\Support\Facades\Event::listen(\Filament\Actions\Imports\Events\ImportStarted::class, function ($event) {
            $options = $event->getOptions();
            $import = $event->getImport();
            $companyId = $options['company_id'] ?? null;
            
            $hasTenancy = \Filament\Facades\Filament::hasTenancy();
            $tenantId = $hasTenancy ? filament()->getTenant()?->id : null;

            // Log de depuración
            \Illuminate\Support\Facades\Log::info('ImportStarted Triggered', [
                'options' => $options,
                'hasTenancy' => $hasTenancy,
                'tenantId' => $tenantId,
                'currentPanel' => \Filament\Facades\Filament::getCurrentPanel()->getId(),
            ]);

            // Si no viene en options, intentamos sacarlo del Tenant actual (Company Panel)
            if (!$companyId && $hasTenancy) {
                $companyId = $tenantId;
            }

            if ($companyId) {
                // Usamos DB::table directo para saltar cualquier restricción del modelo interno de Filament
                \Illuminate\Support\Facades\DB::table('imports')
                    ->where('id', $import->id)
                    ->update(['company_id' => $companyId]);
            }
        });

        // Asignar el company_id al momento de iniciar una exportación
        \Illuminate\Support\Facades\Event::listen(\Filament\Actions\Exports\Events\ExportStarted::class, function ($event) {
            $options = $event->getOptions();
            $export = $event->getExport();
            $companyId = $options['company_id'] ?? null;
            if (!$companyId && \Filament\Facades\Filament::hasTenancy()) {
                $companyId = filament()->getTenant()?->id;
            }

            if ($companyId) {
                \Illuminate\Support\Facades\DB::table('exports')
                    ->where('id', $export->id)
                    ->update(['company_id' => $companyId]);
            }
        });

        // Heredar el company_id de la importación a cada fila fallida
        \Filament\Actions\Imports\Models\FailedImportRow::creating(function ($model) {
            if ($model->import && $model->import->company_id) {
                $model->company_id = $model->import->company_id;
            }
        });

        // Subir archivo a Cloudflare R2 solo cuando la importación de ítems finalice correctamente
        \Illuminate\Support\Facades\Event::listen(\Filament\Actions\Imports\Events\ImportCompleted::class, function ($event) {
            $import = clone $event->getImport(); // Clonar para evitar mutar estado en memoria accidentalmente
            $options = $event->getOptions();

            if ($import->importer !== \App\Filament\Imports\ItemImporter::class) {
                return;
            }

            // Aquí el company_id puede venir de options o lo podemos sacar directamente de la bd:
            $companyId = $import->company_id ?? $options['company_id'] ?? null;
            if (!$companyId) {
                return;
            }

            $filePath = $import->file_path;
            
            if (file_exists($filePath)) {
                $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';
                $storage = \Illuminate\Support\Facades\Storage::disk($disk);
                
                $fileName = basename($filePath);
                if (!\Illuminate\Support\Str::endsWith($fileName, '.csv')) {
                    $fileName .= '.csv';
                }
                
                $r2Path = "companies/company_{$companyId}/items/imports/{$fileName}";
                
                $storage->put($r2Path, file_get_contents($filePath));
                
                @unlink($filePath);
            }
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
