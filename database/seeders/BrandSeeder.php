<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Company;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No se encontraron empresas para asociar las marcas.');

            return;
        }

        $brands = [
            ['name' => 'Dell', 'website' => 'https://www.dell.com'],
            ['name' => 'HP', 'website' => 'https://www.hp.com'],
            ['name' => 'LG', 'website' => 'https://www.lg.com'],
            ['name' => 'Keychron', 'website' => 'https://www.keychron.com'],
            ['name' => 'Logitech', 'website' => 'https://www.logitech.com'],
            ['name' => 'Sony', 'website' => 'https://www.sony.com'],
            ['name' => 'Microsoft', 'website' => 'https://www.microsoft.com'],
        ];

        foreach ($companies as $company) {
            foreach ($brands as $brandData) {
                $brandData['company_id'] = $company->id;
                $brandData['is_active'] = true;

                Brand::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $brandData['name'],
                    ],
                    $brandData
                );
            }
        }

        $this->command->info('✅ Marcas de prueba sembradas correctamente.');
    }
}
