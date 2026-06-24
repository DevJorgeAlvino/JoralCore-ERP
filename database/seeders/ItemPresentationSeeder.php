<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemPresentation;
use App\Models\UnitMeasure;
use Illuminate\Database\Seeder;

class ItemPresentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Item::all();

        if ($items->isEmpty()) {
            $this->command->warn('No se encontraron artículos (items) para generar presentaciones.');

            return;
        }

        foreach ($items as $item) {
            // ─── 1. PRESENTACIÓN BASE (Por defecto) ───────────
            $baseName = $item->type === 'service' ? 'Servicio Base' : 'Unidad';

            $basePresentation = ItemPresentation::updateOrCreate(
                [
                    'item_id' => $item->id,
                    'name' => $baseName,
                ],
                [
                    'unit_measure_id' => $item->unit_measure_id,
                    'conversion_factor' => 1.0000,
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            // Poblar precio general de la presentación base
            $basePresentation->prices()->updateOrCreate(
                [
                    'price_list_name' => 'General',
                    'is_active' => true,
                ],
                [
                    'purchase_cost' => $item->purchase_cost,
                    'sale_price' => $item->sale_price,
                    'currency' => $item->company->currency ?? 'PEN',
                ]
            );

            // Poblar stock inicial de la presentación base
            $basePresentation->stock()->updateOrCreate(
                ['item_presentation_id' => $basePresentation->id],
                [
                    'company_id' => $item->company_id,
                    'current_stock' => $item->current_stock,
                    'minimum_stock' => $item->minimum_stock,
                ]
            );

            // ─── 2. PRESENTACIÓN ADICIONAL (Solo para productos físicos) ───
            if ($item->type === 'product') {
                // Buscamos la unidad de medida "Caja" (BX) de esta empresa
                $cajaUnit = UnitMeasure::where('company_id', $item->company_id)
                    ->where('code', 'BX')
                    ->first();

                // Si no existe, usamos la misma unidad del ítem
                $unitMeasureId = $cajaUnit ? $cajaUnit->id : $item->unit_measure_id;

                $boxPresentation = ItemPresentation::updateOrCreate(
                    [
                        'item_id' => $item->id,
                        'name' => 'Caja x12',
                    ],
                    [
                        'unit_measure_id' => $unitMeasureId,
                        'conversion_factor' => 12.0000,
                        'is_default' => false,
                        'is_active' => true,
                    ]
                );

                // Precio de la caja con un descuento por volumen (cuesta como 11 unidades individuales)
                $boxPresentation->prices()->updateOrCreate(
                    [
                        'price_list_name' => 'General',
                        'is_active' => true,
                    ],
                    [
                        'purchase_cost' => $item->purchase_cost * 11,
                        'sale_price' => $item->sale_price * 11,
                        'currency' => $item->company->currency ?? 'PEN',
                    ]
                );

                // Stock inicial en cajas (stock actual dividido por 12)
                $boxPresentation->stock()->updateOrCreate(
                    ['item_presentation_id' => $boxPresentation->id],
                    [
                        'company_id' => $item->company_id,
                        'current_stock' => floor($item->current_stock / 12),
                        'minimum_stock' => 1.00,
                    ]
                );
            }
        }

        $this->command->info('✅ Presentaciones, precios y stocks de prueba generados correctamente.');
    }
}
