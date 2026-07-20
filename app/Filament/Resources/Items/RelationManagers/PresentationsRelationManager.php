<?php

namespace App\Filament\Resources\Items\RelationManagers;

use App\Models\ItemPresentation;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PresentationsRelationManager extends RelationManager
{
    protected static string $relationship = 'presentations';

    protected static ?string $title = 'Presentaciones';

    protected static \BackedEnum|string|null $icon = 'heroicon-o-archive-box';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('id')
                ->default(fn () => (string) \Illuminate\Support\Str::ulid()),

            Grid::make(12)->columnSpanFull()->schema([
                // Columna Izquierda: Datos, Precios, Stock (8/12)
                Grid::make(1)
                    ->columnSpan(7)
                    ->schema([
                        Section::make('Datos de la Presentación')
                            ->icon('heroicon-o-archive-box')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->placeholder('Ej. Botella 1L, Caja x24, Unidad')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Select::make('unit_measure_id')
                                    ->label('Unidad de Medida')
                                    ->relationship(
                                        name: 'unitMeasure',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->where('company_id', $this->getOwnerRecord()->company_id)
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code} - {$record->name}")
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),

                                TextInput::make('barcode')
                                    ->label('Código de Barras')
                                    ->placeholder('Opcional — código único')
                                    ->maxLength(255),

                                TextInput::make('conversion_factor')
                                    ->label('Factor de Conversión')
                                    ->helperText('Cantidad de unidades base que equivalen a esta presentación. Ej: caja de 24 = 24')
                                    ->numeric()
                                    ->default(1)
                                    ->step('0.0001')
                                    ->minValue(0.0001)
                                    ->columnSpanFull(),

                                Toggle::make('is_default')
                                    ->label('Presentación Principal')
                                    ->helperText('Marca esta como la presentación por defecto del ítem.'),

                                Toggle::make('is_active')
                                    ->label('Activa')
                                    ->default(true),
                            ])->columns(2),
                        Grid::make(1)->schema([
                            Section::make('Precio General')
                                ->description('Precio base para esta presentación.')
                                ->icon('heroicon-o-currency-dollar')
                                ->schema([
                                    TextInput::make('default_purchase_cost')
                                        ->label('Costo de Compra')
                                        ->numeric()
                                        ->prefix('$')
                                        ->step('0.01')
                                        ->default(0),

                                    TextInput::make('default_sale_price')
                                        ->label('Precio de Venta')
                                        ->numeric()
                                        ->prefix('$')
                                        ->step('0.01')
                                        ->default(0),
                                ])->columns(2),

                            Section::make('Stock Inicial')
                                ->description('Stock de esta presentación en bodega.')
                                ->icon('heroicon-o-archive-box-arrow-down')
                                ->schema([
                                    TextInput::make('initial_stock')
                                        ->label('Stock Actual')
                                        ->numeric()
                                        ->default(0)
                                        ->step('0.01')
                                        ->minValue(0),

                                    TextInput::make('minimum_stock')
                                        ->label('Stock Mínimo (Alerta)')
                                        ->numeric()
                                        ->default(0)
                                        ->step('0.01')
                                        ->minValue(0),
                                ])->columns(2),
                        ]),
                    ]),

                // Columna Derecha: Imágenes (4/12)
                Grid::make(1)
                    ->columnSpan(5)
                    ->schema([
                        Section::make('Imágenes Representativas')
                            ->description('Sube imágenes específicas de esta presentación.')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('images')
                                    ->label('Imágenes')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(5)
                                    ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                    ->visibility('public')
                                    ->directory(fn ($get) => "companies/company_{$this->getOwnerRecord()->company_id}/items/{$this->getOwnerRecord()->id}/presentations/presentation_{$get('id')}/images")
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('images')
                    ->label('Imagen')
                    ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                    ->circular()
                    ->limit(1),

                TextColumn::make('name')
                    ->label('Presentación')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->unitMeasure
                        ? "{$record->unitMeasure->code} – {$record->unitMeasure->name}"
                        : null
                    ),

                TextColumn::make('barcode')
                    ->label('Código de Barras')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('conversion_factor')
                    ->label('Factor Conv.')
                    ->numeric(decimalPlaces: 2)
                    ->badge()
                    ->color('gray'),

                TextColumn::make('prices_purchase_cost')
                    ->label('Costo')
                    ->money(fn () => filament()->getTenant()?->currency ?? 'PEN')
                    ->state(fn (ItemPresentation $record) => $record->prices()->where('is_active', true)->latest()->value('purchase_cost') ?? 0)
                    ->toggleable(),

                TextColumn::make('prices_sale_price')
                    ->label('Precio Venta')
                    ->money(fn () => filament()->getTenant()?->currency ?? 'PEN')
                    ->state(fn (ItemPresentation $record) => $record->prices()->where('is_active', true)->latest()->value('sale_price') ?? 0)
                    ->toggleable()
                    ->weight('bold'),

                TextColumn::make('stock_current_stock')
                    ->label('Stock Actual')
                    ->numeric(decimalPlaces: 2)
                    ->state(fn (ItemPresentation $record) => $record->totalStock())
                    ->badge()
                    ->color(fn ($state, ItemPresentation $record) => $state <= ($record->stocks()->sum('minimum_stock') ?? 0) ? 'danger' : 'success'),

                IconColumn::make('is_default')
                    ->label('Principal')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('7xl')
                    ->label('Nueva Presentación')
                    ->using(function (array $data, string $model): ItemPresentation {
                        // Crear la presentación
                        $presentation = $this->getOwnerRecord()->presentations()->create([
                            'id' => $data['id'],
                            'unit_measure_id' => $data['unit_measure_id'],
                            'name' => $data['name'],
                            'barcode' => $data['barcode'] ?? null,
                            'images' => $data['images'] ?? null,
                            'conversion_factor' => $data['conversion_factor'] ?? 1,
                            'is_default' => $data['is_default'] ?? false,
                            'is_active' => $data['is_active'] ?? true,
                        ]);

                        // Crear precio por defecto si se ingresaron valores
                        if (($data['default_purchase_cost'] ?? 0) > 0 || ($data['default_sale_price'] ?? 0) > 0) {
                            $presentation->prices()->create([
                                'price_list_name' => 'General',
                                'purchase_cost' => $data['default_purchase_cost'] ?? 0,
                                'sale_price' => $data['default_sale_price'] ?? 0,
                                'currency' => filament()->getTenant()?->currency ?? 'PEN',
                                'is_active' => true,
                            ]);
                        }

                        // Crear stock inicial asociado al almacén por defecto y registrar en Kardex
                        $defaultWarehouseId = \App\Models\Warehouse::where('company_id', $this->getOwnerRecord()->company_id)
                            ->where('is_default', true)
                            ->value('id')
                            ?? \App\Models\Warehouse::where('company_id', $this->getOwnerRecord()->company_id)
                            ->value('id');

                        if ($defaultWarehouseId) {
                            $initialStock = $data['initial_stock'] ?? 0;

                            $presentation->stocks()->create([
                                'warehouse_id' => $defaultWarehouseId,
                                'current_stock' => $initialStock,
                                'minimum_stock' => $data['minimum_stock'] ?? 0,
                            ]);

                            if ($initialStock > 0) {
                                $userId = auth()->id() ?? \App\Models\User::value('id');

                                \App\Models\InventoryMovement::create([
                                    'company_id' => $this->getOwnerRecord()->company_id,
                                    'warehouse_id' => $defaultWarehouseId,
                                    'item_presentation_id' => $presentation->id,
                                    'user_id' => $userId,
                                    'type' => 'in',
                                    'concept' => 'Inventario Inicial',
                                    'quantity' => $initialStock,
                                    'unit_cost' => $data['default_purchase_cost'] ?? 0,
                                    'balance_stock' => $initialStock,
                                    'reference_document' => 'presentation_init',
                                    'reference_number' => 'REG-PRES-' . $presentation->id,
                                ]);
                            }
                        }

                        return $presentation;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->modalWidth('7xl')
                    ->using(function (ItemPresentation $record, array $data): ItemPresentation {
                        $record->update([
                            'unit_measure_id' => $data['unit_measure_id'],
                            'name' => $data['name'],
                            'barcode' => $data['barcode'] ?? null,
                            'images' => $data['images'] ?? null,
                            'conversion_factor' => $data['conversion_factor'] ?? 1,
                            'is_default' => $data['is_default'] ?? false,
                            'is_active' => $data['is_active'] ?? true,
                        ]);

                        // Actualizar stock del almacén por defecto y registrar Kardex si cambia
                        $defaultWarehouseId = \App\Models\Warehouse::where('company_id', $record->item->company_id)
                            ->where('is_default', true)
                            ->value('id')
                            ?? \App\Models\Warehouse::where('company_id', $record->item->company_id)
                            ->value('id');

                        if ($defaultWarehouseId) {
                            $oldStockRecord = $record->stocks()->where('warehouse_id', $defaultWarehouseId)->first();
                            $oldStock = $oldStockRecord ? (float) $oldStockRecord->current_stock : 0.0000;
                            $newStock = (float) ($data['initial_stock'] ?? 0.0000);

                            if ($newStock != $oldStock) {
                                $diff = $newStock - $oldStock;
                                $type = $diff > 0 ? 'in' : 'out';
                                $quantity = abs($diff);

                                $record->stocks()->updateOrCreate(
                                    ['warehouse_id' => $defaultWarehouseId],
                                    [
                                        'current_stock' => $newStock,
                                        'minimum_stock' => $data['minimum_stock'] ?? 0,
                                    ]
                                );

                                $userId = auth()->id() ?? \App\Models\User::value('id');

                                \App\Models\InventoryMovement::create([
                                    'company_id' => $record->item->company_id,
                                    'warehouse_id' => $defaultWarehouseId,
                                    'item_presentation_id' => $record->id,
                                    'user_id' => $userId,
                                    'type' => $type,
                                    'concept' => 'Ajuste por Edición',
                                    'quantity' => $quantity,
                                    'unit_cost' => $data['default_purchase_cost'] ?? 0,
                                    'balance_stock' => $newStock,
                                    'reference_document' => 'manual_edit',
                                    'reference_number' => 'EDIT-PRES-' . $record->id,
                                ]);
                            } else {
                                $record->stocks()->updateOrCreate(
                                    ['warehouse_id' => $defaultWarehouseId],
                                    [
                                        'minimum_stock' => $data['minimum_stock'] ?? 0,
                                    ]
                                );
                            }
                        }

                        // Actualizar precio
                        $record->prices()->updateOrCreate(
                            [
                                'item_presentation_id' => $record->id,
                                'price_list_name' => 'General',
                                'is_active' => true,
                            ],
                            [
                                'purchase_cost' => $data['default_purchase_cost'] ?? 0,
                                'sale_price' => $data['default_sale_price'] ?? 0,
                                'currency' => filament()->getTenant()?->currency ?? 'PEN',
                            ]
                        );

                        return $record;
                    })
                    ->fillForm(fn (ItemPresentation $record) => [
                        'id' => $record->id,
                        'unit_measure_id' => $record->unit_measure_id,
                        'name' => $record->name,
                        'barcode' => $record->barcode,
                        'images' => $record->images ?? [],
                        'conversion_factor' => $record->conversion_factor,
                        'is_default' => $record->is_default,
                        'is_active' => $record->is_active,
                        'initial_stock' => (float) ($record->stocks()->where('warehouse_id', \App\Models\Warehouse::where('company_id', $record->item->company_id)->where('is_default', true)->value('id') ?? \App\Models\Warehouse::where('company_id', $record->item->company_id)->value('id'))->value('current_stock') ?? 0),
                        'minimum_stock' => (float) ($record->stocks()->where('warehouse_id', \App\Models\Warehouse::where('company_id', $record->item->company_id)->where('is_default', true)->value('id') ?? \App\Models\Warehouse::where('company_id', $record->item->company_id)->value('id'))->value('minimum_stock') ?? 0),
                        'default_purchase_cost' => $record->prices()->where('is_active', true)->latest()->value('purchase_cost') ?? 0,
                        'default_sale_price' => $record->prices()->where('is_active', true)->latest()->value('sale_price') ?? 0,
                    ]),

                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
