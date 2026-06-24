<?php

namespace App\Filament\Resources\Items\RelationManagers;

use App\Models\ItemPresentation;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
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
                        ->placeholder('Opcional — código único de esta presentación')
                        ->maxLength(255),

                    TextInput::make('conversion_factor')
                        ->label('Factor de Conversión')
                        ->helperText('Cantidad de unidades base que equivalen a esta presentación. Ej: caja de 24 = 24')
                        ->numeric()
                        ->default(1)
                        ->step('0.0001')
                        ->minValue(0.0001),
                ])->columns(2),

            Section::make('Precio General')
                ->description('Precio base para esta presentación. Puedes agregar más listas de precio desde la sección de precios.')
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

            Section::make('Estado')->schema([
                Toggle::make('is_default')
                    ->label('Presentación Principal')
                    ->helperText('Marca esta como la presentación por defecto del ítem.'),

                Toggle::make('is_active')
                    ->label('Activa')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
                    ->modalWidth('4xl')
                    ->label('Nueva Presentación')
                    ->using(function (array $data, string $model): ItemPresentation {
                        // Crear la presentación
                        $presentation = $this->getOwnerRecord()->presentations()->create([
                            'unit_measure_id' => $data['unit_measure_id'],
                            'name' => $data['name'],
                            'barcode' => $data['barcode'] ?? null,
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

                        // Crear stock inicial
                        $presentation->stock()->create([
                            'company_id' => $this->getOwnerRecord()->company_id,
                            'current_stock' => $data['initial_stock'] ?? 0,
                            'minimum_stock' => $data['minimum_stock'] ?? 0,
                        ]);

                        return $presentation;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->modalWidth('4xl')
                    ->using(function (ItemPresentation $record, array $data): ItemPresentation {
                        $record->update([
                            'unit_measure_id' => $data['unit_measure_id'],
                            'name' => $data['name'],
                            'barcode' => $data['barcode'] ?? null,
                            'conversion_factor' => $data['conversion_factor'] ?? 1,
                            'is_default' => $data['is_default'] ?? false,
                            'is_active' => $data['is_active'] ?? true,
                        ]);

                        // Actualizar stock
                        $record->stock()->updateOrCreate(
                            ['item_presentation_id' => $record->id],
                            [
                                'company_id' => $record->item->company_id,
                                'current_stock' => $data['initial_stock'] ?? $record->stock?->current_stock ?? 0,
                                'minimum_stock' => $data['minimum_stock'] ?? $record->stock?->minimum_stock ?? 0,
                            ]
                        );

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
                        'unit_measure_id' => $record->unit_measure_id,
                        'name' => $record->name,
                        'barcode' => $record->barcode,
                        'conversion_factor' => $record->conversion_factor,
                        'is_default' => $record->is_default,
                        'is_active' => $record->is_active,
                        'initial_stock' => $record->stock?->current_stock ?? 0,
                        'minimum_stock' => $record->stock?->minimum_stock ?? 0,
                        'default_purchase_cost' => $record->prices()->where('is_active', true)->latest()->value('purchase_cost') ?? 0,
                        'default_sale_price' => $record->prices()->where('is_active', true)->latest()->value('sale_price') ?? 0,
                    ]),

                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
