<?php

namespace App\Filament\Imports;

use App\Models\Item;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ItemImporter extends Importer
{
    protected static ?string $model = Item::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nombre del Ítem')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            
            ImportColumn::make('sku')
                ->label('SKU')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('barcode')
                ->label('Código de Barras')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('type')
                ->label('Tipo (product/service)')
                ->requiredMapping()
                ->rules(['required', 'in:product,service']),

            ImportColumn::make('unit_code')
                ->label('Cód. Unidad Medida')
                ->rules(['nullable', 'max:5']),

            ImportColumn::make('purchase_cost')
                ->label('Costo de Compra')
                ->numeric()
                ->rules(['nullable', 'numeric']),

            ImportColumn::make('sale_price')
                ->label('Precio de Venta')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),

            ImportColumn::make('manage_stock')
                ->label('Gestiona Stock? (1 o 0)')
                ->boolean()
                ->rules(['nullable', 'boolean']),

            ImportColumn::make('current_stock')
                ->label('Stock Actual')
                ->numeric()
                ->rules(['nullable', 'numeric']),

            ImportColumn::make('minimum_stock')
                ->label('Stock Mínimo')
                ->numeric()
                ->rules(['nullable', 'numeric']),

            ImportColumn::make('is_active')
                ->label('Es Activo? (1 o 0)')
                ->boolean()
                ->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Item
    {
        $item = new Item();
        
        // Obtener el ID de la empresa a través del Formulario de Importación (Admin)
        // O a través del Tenant Activo (Company Panel)
        $companyId = $this->options['company_id'] ?? filament()->getTenant()?->id;

        if ($companyId) {
            $item->company_id = $companyId;
        }

        // Configuración por defecto si no vienen en el archivo
        if (!isset($this->data['manage_stock'])) {
            $item->manage_stock = false;
        }
        if (!isset($this->data['is_active'])) {
            $item->is_active = true;
        }

        return $item;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Se han importado ' . Number::format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . ' de forma exitosa.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Sin embargo, ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
