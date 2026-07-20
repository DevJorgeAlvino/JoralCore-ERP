<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use App\Models\ItemPresentation;
use App\Models\Warehouse;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class InventoryMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([

                // ── Left (main) ─────────────────────────────────────────────
                Group::make()->columnSpan(['lg' => 2])->schema([

                    Section::make('Detalle del Movimiento')
                        ->description('Registra una entrada o salida de inventario de forma manual.')
                        ->icon('heroicon-o-arrows-right-left')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('company_id')
                                    ->label('Empresa')
                                    ->relationship('company', 'name')
                                    ->required()
                                    ->default(fn () => Filament::getTenant()?->id)
                                    ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin')
                                    ->dehydrated(true)
                                    ->live()
                                    ->native(false)
                                    ->columnSpanFull(),

                                Select::make('warehouse_id')
                                    ->label('Almacén')
                                    ->options(function (Get $get) {
                                        $companyId = Filament::getCurrentPanel()?->getId() === 'admin'
                                            ? $get('company_id')
                                            : Filament::getTenant()?->id;

                                        if (!$companyId) {
                                            return [];
                                        }

                                        return Warehouse::where('company_id', $companyId)
                                            ->where('is_active', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->native(false),

                                Select::make('item_presentation_id')
                                    ->label('Producto (Presentación)')
                                    ->options(function (Get $get) {
                                        $companyId = Filament::getCurrentPanel()?->getId() === 'admin'
                                            ? $get('company_id')
                                            : Filament::getTenant()?->id;

                                        if (!$companyId) {
                                            return [];
                                        }

                                        return ItemPresentation::whereHas('item', function ($query) use ($companyId) {
                                            $query->where('company_id', $companyId);
                                        })
                                        ->with(['item', 'unitMeasure'])
                                        ->get()
                                        ->mapWithKeys(function ($presentation) {
                                            $unitCode = $presentation->unitMeasure?->code ?? '—';
                                            $label = "{$presentation->item->name} - {$presentation->name} ({$unitCode})";
                                            return [$presentation->id => $label];
                                        });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false),

                                Select::make('type')
                                    ->label('Tipo de Movimiento')
                                    ->options([
                                        'in' => 'Entrada (+ Suma Stock)',
                                        'out' => 'Salida (- Resta Stock)',
                                    ])
                                    ->required()
                                    ->live()
                                    ->native(false),

                                Select::make('concept')
                                    ->label('Concepto')
                                    ->options(fn (Get $get) => $get('type') === 'in' ? [
                                        'Inventario Inicial' => 'Inventario Inicial',
                                        'Compra' => 'Compra / Ingreso Proveedor',
                                        'Ajuste por Sobrante' => 'Ajuste por Sobrante',
                                        'Devolución Cliente' => 'Devolución de Cliente',
                                        'Otros Ingresos' => 'Otros Ingresos',
                                    ] : [
                                        'Ajuste por Merma / Pérdida' => 'Ajuste por Merma / Pérdida',
                                        'Consumo Interno' => 'Consumo Interno / Uso propio',
                                        'Devolución Proveedor' => 'Devolución a Proveedor',
                                        'Otros Egresos' => 'Otros Egresos',
                                    ])
                                    ->required()
                                    ->native(false),

                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.0001)
                                    ->step('0.0001')
                                    ->required(),

                                TextInput::make('unit_cost')
                                    ->label('Costo Unitario')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.00)
                                    ->step('0.01')
                                    ->required(),
                            ])
                        ]),
                ]),

                // ── Right (references) ───────────────────────────────────────
                Group::make()->columnSpan(['lg' => 1])->schema([

                    Section::make('Referencia')
                        ->description('Documentos de respaldo.')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            TextInput::make('reference_document')
                                ->label('Documento de Respaldo')
                                ->placeholder('Ej. Factura, Boleta, Guía, Nro Ajuste')
                                ->maxLength(255),

                            TextInput::make('reference_number')
                                ->label('Número de Referencia')
                                ->placeholder('Ej. F001-12345')
                                ->maxLength(255),

                            Hidden::make('user_id')
                                ->default(fn () => auth()->id()),
                        ]),
                ]),
            ]),
        ]);
    }
}
