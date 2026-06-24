<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No se encontraron empresas para asociar las categorías.');

            return;
        }

        $categories = [
            [
                'name' => 'Tecnología',
                'description' => 'Dispositivos electrónicos, computadoras, accesorios y TI.',
                'icon' => 'heroicon-o-cpu-chip',
                'color' => '#3B82F6',
                'children' => [
                    [
                        'name' => 'Laptops',
                        'description' => 'Computadoras portátiles y notebooks.',
                        'icon' => 'heroicon-o-computer-desktop',
                        'color' => '#2563EB',
                    ],
                    [
                        'name' => 'Accesorios',
                        'description' => 'Teclados, mouses, monitores y cables.',
                        'icon' => 'heroicon-o-keyboard',
                        'color' => '#1D4ED8',
                    ],
                ],
            ],
            [
                'name' => 'Servicios',
                'description' => 'Servicios de consultoría, mantenimiento y licencias.',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'color' => '#10B981',
                'children' => [
                    [
                        'name' => 'Soporte TI',
                        'description' => 'Mantenimiento de servidores e infraestructura.',
                        'icon' => 'heroicon-o-server',
                        'color' => '#059669',
                    ],
                    [
                        'name' => 'Licenciamiento',
                        'description' => 'Suscripciones y licencias de software.',
                        'icon' => 'heroicon-o-key',
                        'color' => '#047857',
                    ],
                ],
            ],
            [
                'name' => 'Oficina',
                'description' => 'Útiles de escritorio, papel y papelería.',
                'icon' => 'heroicon-o-archive-box',
                'color' => '#F59E0B',
            ],
        ];

        foreach ($companies as $company) {
            foreach ($categories as $catData) {
                $children = $catData['children'] ?? [];
                unset($catData['children']);

                $catData['company_id'] = $company->id;
                $catData['is_active'] = true;

                $parent = Category::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $catData['name'],
                    ],
                    $catData
                );

                foreach ($children as $childData) {
                    $childData['company_id'] = $company->id;
                    $childData['parent_id'] = $parent->id;
                    $childData['is_active'] = true;

                    Category::updateOrCreate(
                        [
                            'company_id' => $company->id,
                            'parent_id' => $parent->id,
                            'name' => $childData['name'],
                        ],
                        $childData
                    );
                }
            }
        }

        $this->command->info('✅ Categorías de prueba sembradas correctamente.');
    }
}
