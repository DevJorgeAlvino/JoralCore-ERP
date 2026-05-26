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
                ->label(__('items.infolist.fields.name'))
                ->guess(['nombre', 'producto', 'articulo', 'name'])
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            
            ImportColumn::make('sku')
                ->label(__('items.infolist.fields.sku'))
                ->guess(['sku', 'codigo', 'codigo sku'])
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('barcode')
                ->label(__('items.infolist.fields.barcode'))
                ->guess(['barcode', 'codigo de barras', 'ean', 'upc'])
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('type')
                ->label(__('items.infolist.fields.item_type'))
                ->guess(['tipo', 'type', 'clase'])
                ->requiredMapping()
                ->rules(['required', 'in:product,service']),

            ImportColumn::make('unit_measure_code')
                ->label(__('items.infolist.fields.unit_measure'))
                ->guess(['unidad', 'unidad medida', 'unit', 'unit_code', 'medida'])
                ->rules(['nullable', 'max:10']),

            ImportColumn::make('purchase_cost')
                ->label(__('items.infolist.fields.purchase_cost'))
                ->guess(['costo', 'costo de compra', 'purchase_cost', 'precio compra'])
                ->numeric()
                ->rules(['nullable', 'numeric']),

            ImportColumn::make('sale_price')
                ->label(__('items.infolist.fields.sale_price'))
                ->guess(['precio', 'precio de venta', 'sale_price', 'precio venta'])
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),

            ImportColumn::make('manage_stock')
                ->label(__('items.infolist.fields.manage_stock'))
                ->guess(['gestiona stock', 'controla stock', 'manage_stock', 'stock_control'])
                ->boolean()
                ->rules(['nullable', 'boolean']),

            ImportColumn::make('current_stock')
                ->label(__('items.infolist.fields.current_stock'))
                ->guess(['stock', 'stock actual', 'current_stock', 'cantidad', 'inventario'])
                ->numeric()
                ->rules(['nullable', 'numeric']),

            ImportColumn::make('minimum_stock')
                ->label(__('items.infolist.fields.minimum_stock'))
                ->guess(['stock minimo', 'minimum_stock', 'minimo'])
                ->numeric()
                ->rules(['nullable', 'numeric']),

            ImportColumn::make('is_active')
                ->label(__('items.infolist.fields.is_active'))
                ->guess(['activo', 'is_active', 'estado'])
                ->boolean()
                ->rules(['nullable', 'boolean']),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            \Filament\Forms\Components\Select::make('company_id')
                ->label(__('items.infolist.fields.company'))
                ->options(\App\Models\Company::pluck('name', 'id'))
                ->required()
                ->searchable()
                // Solo se muestra en el panel de Administración
                ->visible(fn () => \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin'),
        ];
    }

    public function resolveRecord(): ?Item
    {
        $item = new Item();
        
        // Obtener el ID de la empresa a través del Formulario de Importación (Admin)
        // O a través del Tenant Activo (Company Panel)
        $companyId = $this->options['company_id'] ?? filament()->getTenant()?->id;

        if (!$companyId) {
            throw new \Filament\Actions\Imports\Exceptions\RowImportFailedException('No se ha podido asignar una Empresa (Tenant) a este ítem.');
        }

        // Forzar la actualización del company_id en la tabla imports si aún no lo tiene
        if (!$this->import->company_id) {
            \Illuminate\Support\Facades\DB::table('imports')
                ->where('id', $this->import->id)
                ->update(['company_id' => $companyId]);
            $this->import->company_id = $companyId;
        }

        $item->company_id = $companyId;

        // Configuración por defecto si no vienen en el archivo
        if (!isset($this->data['manage_stock'])) {
            $item->manage_stock = false;
        }
        if (!isset($this->data['is_active'])) {
            $item->is_active = true;
        }

        // Resolver unit_measure_id desde el codigo de unidad
        if (!empty($this->data['unit_measure_code'])) {
            $unitMeasure = \App\Models\UnitMeasure::where('code', $this->data['unit_measure_code'])
                ->where('company_id', $companyId)
                ->first();

            if ($unitMeasure) {
                $item->unit_measure_id = $unitMeasure->id;
            }
        }

        // Limpiar el campo para que no intente guardarse como columna
        unset($this->data['unit_measure_code']);

        return $item;
    }

    public function saveRecord(): void
    {
        
        try {
            parent::saveRecord();
        } catch (\Illuminate\Database\QueryException $e) {
            throw new \Filament\Actions\Imports\Exceptions\RowImportFailedException('Error de base de datos: ' . $e->errorInfo[2]);
        } catch (\Throwable $e) {
            throw new \Filament\Actions\Imports\Exceptions\RowImportFailedException('Error interno: ' . $e->getMessage());
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Se han importado ' . Number::format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . ' de forma exitosa.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Sin embargo, ' . Number::format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }

    /**
     * Middleware de Queue para evitar que una misma empresa ejecute múltiples importaciones simultáneas.
     * Si envían otra mientras hay una procesando, se encola y espera 60 segundos antes de reintentar.
     */
    public function getJobMiddleware(): array
    {
        return [
            (new \Illuminate\Queue\Middleware\WithoutOverlapping($this->import->company_id))->releaseAfter(60),
        ];
    }

    /**
     * Inyecta el company_id en la notificación de completado para que el filtro
     * por empresa funcione incluso cuando el job corre en background (sin contexto HTTP).
     */
    public static function modifyCompletedNotification(\Filament\Notifications\Notification $notification, Import $import): \Filament\Notifications\Notification
    {
        // Pasamos el company_id a través de viewData para que el observer
        // DatabaseNotification::creating en AppServiceProvider lo capture y
        // lo coloque en la raíz del JSON, filtrando correctamente la notificación.
        if ($import->company_id) {
            $notification->viewData(['company_id' => $import->company_id]);
        }

        return $notification;
    }
}
