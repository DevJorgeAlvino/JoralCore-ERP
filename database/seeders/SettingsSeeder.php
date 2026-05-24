<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Services\CompanySettingService;
use App\Services\SystemSettingService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed system and company settings with sensible defaults.
     */
    public function run(): void
    {
        // ─── Global System Settings ──────────────────
        $systemDefaults = [
            'app_name'        => 'JoralERP',
            'app_logo'        => null,
            'app_favicon'     => null,
            'color_primary'   => '#f59e0b',
            'color_secondary' => '#6366f1',
            'default_locale'  => 'es',
        ];

        foreach ($systemDefaults as $key => $value) {
            SystemSettingService::set($key, $value);
        }

        $this->command->info('✅ Configuración global del sistema cargada.');

        // ─── Company Settings: Perú ──────────────────
        $peCompany = Company::where('slug', 'joral-solutions-pe')->first();

        if ($peCompany) {
            $peSettings = [
                // Branding
                'company_logo'        => null,
                'company_icon'        => null,
                'primary_color'       => '#dc2626',  // Rojo 🇵🇪
                'secondary_color'     => '#991b1b',
                'locale'              => 'es',
                // Operaciones
                'business_hours' => [
                    ['day' => 'lunes',     'open' => '08:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'martes',    'open' => '08:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'miércoles', 'open' => '08:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'jueves',    'open' => '08:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'viernes',   'open' => '08:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'sábado',    'open' => '09:00', 'close' => '13:00', 'active' => true],
                ],
                'secondary_currency'  => 'USD',
                'exchange_rate'       => 3.72,
                'stock_alert_min'     => 10,
                'stock_alert_enabled' => true,
            ];

            foreach ($peSettings as $key => $value) {
                CompanySettingService::set($peCompany->id, $key, $value);
            }

            $this->command->info("✅ Settings de '{$peCompany->name}' cargados.");
        }

        // ─── Company Settings: Chile ─────────────────
        $clCompany = Company::where('slug', 'joral-solutions-cl')->first();

        if ($clCompany) {
            $clSettings = [
                // Branding
                'company_logo'        => null,
                'company_icon'        => null,
                'primary_color'       => '#2563eb',  // Azul 🇨🇱
                'secondary_color'     => '#1e40af',
                'locale'              => 'es',
                // Operaciones
                'business_hours' => [
                    ['day' => 'lunes',     'open' => '09:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'martes',    'open' => '09:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'miércoles', 'open' => '09:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'jueves',    'open' => '09:00', 'close' => '18:00', 'active' => true],
                    ['day' => 'viernes',   'open' => '09:00', 'close' => '18:00', 'active' => true],
                ],
                'secondary_currency'  => 'USD',
                'exchange_rate'       => 930.50,
                'stock_alert_min'     => 5,
                'stock_alert_enabled' => true,
            ];

            foreach ($clSettings as $key => $value) {
                CompanySettingService::set($clCompany->id, $key, $value);
            }

            $this->command->info("✅ Settings de '{$clCompany->name}' cargados.");
        }
    }
}
