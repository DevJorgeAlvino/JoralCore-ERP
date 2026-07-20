<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No se encontraron empresas para asociar almacenes.');
            return;
        }

        foreach ($companies as $company) {
            // Almacén Principal (Predeterminado)
            Warehouse::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'code' => 'ALM-PRIN',
                ],
                [
                    'name' => 'Almacén Principal',
                    'address' => 'Av. Industrial 520, Oficina 2',
                    'city' => $company->country === 'CL' ? 'Santiago' : 'Lima',
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            // Almacén de Mermas / Secundario
            Warehouse::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'code' => 'ALM-SEC',
                ],
                [
                    'name' => 'Almacén Secundario / Mermas',
                    'address' => 'Calle Secundaria 104',
                    'city' => $company->country === 'CL' ? 'Santiago' : 'Lima',
                    'is_default' => false,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Almacenes de prueba sembrados correctamente.');
    }
}
