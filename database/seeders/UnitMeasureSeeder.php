<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\UnitMeasure;
use Illuminate\Database\Seeder;

class UnitMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $measures = [
            ['code' => 'NIU', 'name' => 'Unidad (Bienes)', 'country' => 'PE'],
            ['code' => 'ZZ',  'name' => 'Servicio', 'country' => 'PE'],
            ['code' => 'UN',  'name' => 'Unidad (Chile)', 'country' => 'CL'],
            ['code' => 'KG',  'name' => 'Kilogramos', 'country' => null],
            ['code' => 'BX',  'name' => 'Caja', 'country' => null],
            ['code' => 'MT',  'name' => 'Metros', 'country' => null],
        ];

        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No se encontraron empresas para asociar las unidades de medida.');

            return;
        }

        foreach ($companies as $company) {
            foreach ($measures as $measure) {
                // Omitir medidas de otros países
                if ($measure['country'] !== null && $measure['country'] !== $company->country) {
                    continue;
                }

                UnitMeasure::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'code' => $measure['code'],
                    ],
                    [
                        'name' => $measure['name'],
                        'country' => $measure['country'],
                    ]
                );
            }
        }
    }
}
