<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Item;
use App\Models\UnitMeasure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No se encontró ninguna empresa. Por favor, crea una empresa primero.');

            return;
        }

        $items = [
            [
                'name' => 'Laptop Dell XPS 13',
                'sku' => 'IT-LT-001',
                'type' => 'product',
                'unit_code' => 'NIU',
                'category_name' => 'Laptops',
                'brand_name' => 'Dell',
                'purchase_cost' => 850.00,
                'sale_price' => 1200.00,
                'manage_stock' => true,
                'current_stock' => 15,
                'minimum_stock' => 5,
            ],
            [
                'name' => 'Monitor LG UltraWide 34"',
                'sku' => 'IT-MN-002',
                'type' => 'product',
                'unit_code' => 'NIU',
                'category_name' => 'Accesorios',
                'brand_name' => 'LG',
                'purchase_cost' => 300.00,
                'sale_price' => 450.00,
                'manage_stock' => true,
                'current_stock' => 8,
                'minimum_stock' => 3,
            ],
            [
                'name' => 'Teclado Mecánico Keychron K2',
                'sku' => 'IT-KB-003',
                'type' => 'product',
                'unit_code' => 'NIU',
                'category_name' => 'Accesorios',
                'brand_name' => 'Keychron',
                'purchase_cost' => 60.00,
                'sale_price' => 95.00,
                'manage_stock' => true,
                'current_stock' => 20,
                'minimum_stock' => 10,
            ],
            [
                'name' => 'Mouse Logitech MX Master 3',
                'sku' => 'IT-MS-004',
                'type' => 'product',
                'unit_code' => 'NIU',
                'category_name' => 'Accesorios',
                'brand_name' => 'Logitech',
                'purchase_cost' => 70.00,
                'sale_price' => 110.00,
                'manage_stock' => true,
                'current_stock' => 12,
                'minimum_stock' => 5,
            ],
            [
                'name' => 'Audífonos Sony WH-1000XM5',
                'sku' => 'IT-AD-005',
                'type' => 'product',
                'unit_code' => 'NIU',
                'category_name' => 'Accesorios',
                'brand_name' => 'Sony',
                'purchase_cost' => 250.00,
                'sale_price' => 350.00,
                'manage_stock' => true,
                'current_stock' => 5,
                'minimum_stock' => 2,
            ],
            [
                'name' => 'Cable de Red CAT6 (Por Metro)',
                'sku' => 'IT-CB-006',
                'type' => 'product',
                'unit_code' => 'MT',
                'category_name' => 'Accesorios',
                'brand_name' => null,
                'purchase_cost' => 1.50,
                'sale_price' => 3.50,
                'manage_stock' => true,
                'current_stock' => 500,
                'minimum_stock' => 100,
            ],
            [
                'name' => 'Suscripción Office 365 Anual',
                'sku' => 'SV-OF-007',
                'type' => 'service',
                'unit_code' => 'ZZ',
                'category_name' => 'Licenciamiento',
                'brand_name' => 'Microsoft',
                'purchase_cost' => 50.00,
                'sale_price' => 85.00,
                'manage_stock' => false,
                'current_stock' => 0,
                'minimum_stock' => 0,
            ],
            [
                'name' => 'Mantenimiento Preventivo de Servidor',
                'sku' => 'SV-MT-008',
                'type' => 'service',
                'unit_code' => 'ZZ',
                'category_name' => 'Soporte TI',
                'brand_name' => null,
                'purchase_cost' => 0.00,
                'sale_price' => 150.00,
                'manage_stock' => false,
                'current_stock' => 0,
                'minimum_stock' => 0,
            ],
            [
                'name' => 'Caja de Hojas Bond A4 (500 unid)',
                'sku' => 'OF-HJ-009',
                'type' => 'product',
                'unit_code' => 'BX',
                'category_name' => 'Oficina',
                'brand_name' => null,
                'purchase_cost' => 12.00,
                'sale_price' => 18.00,
                'manage_stock' => true,
                'current_stock' => 50,
                'minimum_stock' => 15,
            ],
            [
                'name' => 'Consultoría TI Especializada',
                'sku' => 'SV-CT-010',
                'type' => 'service',
                'unit_code' => 'ZZ',
                'category_name' => 'Servicios',
                'brand_name' => null,
                'purchase_cost' => 0.00,
                'sale_price' => 90.00,
                'manage_stock' => false,
                'current_stock' => 0,
                'minimum_stock' => 0,
            ],
        ];

        foreach ($companies as $company) {
            foreach ($items as $itemData) {
                $unitCode = $itemData['unit_code'] ?? 'NIU';
                $categoryName = $itemData['category_name'];
                $brandName = $itemData['brand_name'];
                unset($itemData['unit_code'], $itemData['category_name'], $itemData['brand_name']);

                $unitMeasure = UnitMeasure::where('company_id', $company->id)
                    ->where('code', $unitCode)
                    ->first();

                $category = Category::where('company_id', $company->id)
                    ->where('name', $categoryName)
                    ->first();

                $brand = $brandName ? Brand::where('company_id', $company->id)
                    ->where('name', $brandName)
                    ->first() : null;

                $itemData['unit_measure_id'] = $unitMeasure?->id;
                $itemData['category_id'] = $category?->id;
                $itemData['brand_id'] = $brand?->id;
                $itemData['company_id'] = $company->id;
                $itemData['tax_type'] = 'gravado';
                $itemData['is_active'] = true;
                $itemData['slug'] = Str::slug($itemData['name']);

                Item::updateOrCreate(
                    ['sku' => $itemData['sku'], 'company_id' => $company->id],
                    $itemData
                );
            }
        }

        $this->command->info('✅ Ítems de prueba (Productos y Servicios) insertados exitosamente.');
    }
}
