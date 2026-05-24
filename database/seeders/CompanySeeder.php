<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Seed the application's database with demo companies for PE and CL.
     */
    public function run(): void
    {
        $companies = [
            // ─── Empresa Perú ────────────────────────────
            [
                'name'                   => 'Joral Solutions Perú',
                'slug'                   => 'joral-solutions-pe',
                'description'            => 'Sede central de operaciones y distribución en Perú.',
                'legal_name'             => 'JORAL SOLUTIONS S.A.C.',
                'identity_document'      => '20601234567',
                'dv'                     => null,
                'economic_activity_code' => '6201',
                'country'                => 'PE',
                'currency'               => 'PEN',
                'timezone'               => 'America/Lima',
                'tax_address'            => 'Av. Víctor Raúl Haya de la Torre Nro. 450, Urb. El Trapecio, Chimbote, Ancash',
                'geo_code'               => '021301',
                'phone'                  => '+51945678912',
                'email'                  => 'contacto@joralsolutions.pe',
            ],

            // ─── Empresa Chile ───────────────────────────
            [
                'name'                   => 'Joral Solutions Chile',
                'slug'                   => 'joral-solutions-cl',
                'description'            => 'Sucursal comercial para la gestión de PYMEs en Santiago.',
                'legal_name'             => 'JORAL SOLUTIONS CHILE SPA',
                'identity_document'      => '76123456',
                'dv'                     => '7',
                'economic_activity_code' => '620200',
                'country'                => 'CL',
                'currency'               => 'CLP',
                'timezone'               => 'America/Santiago',
                'tax_address'            => "Av. Libertador Bernardo O'Higgins 1240, Santiago Centro, Región Metropolitana",
                'geo_code'               => '13101',
                'phone'                  => '+56223456789',
                'email'                  => 'contacto@joralsolutions.cl',
            ],
        ];

        foreach ($companies as $data) {
            Company::updateOrCreate(
                ['slug' => $data['slug']],
                $data,
            );
        }
    }
}
