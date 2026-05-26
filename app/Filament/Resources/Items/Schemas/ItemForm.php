<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                ->columnSpanFull()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Información Principal')
                                    ->description('Datos básicos de identificación y descripción del artículo.')
                                    ->icon('heroicon-o-cube')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre del Artículo')
                                            ->placeholder('Ej. Laptop Dell XPS 13')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->maxLength(255)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                            ->columnSpanFull(),

                                        TextInput::make('sku')
                                            ->label('SKU (Código Interno)')
                                            ->placeholder('Ej. IT-LT-001')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(
                                                table: 'items',
                                                column: 'sku',
                                                ignoreRecord: true,
                                                modifyRuleUsing: function (Unique $rule) {
                                                    $tenant = Filament::getTenant();
                                                    return $tenant ? $rule->where('company_id', $tenant->id) : $rule;
                                                }
                                            ),

                                        TextInput::make('barcode')
                                            ->label('Código de Barras')
                                            ->placeholder('Escanea o ingresa el código (Opcional)')
                                            ->maxLength(255),

                                        Hidden::make('slug'),

                                        Textarea::make('description')
                                            ->label('Descripción Detallada')
                                            ->placeholder('Características, especificaciones o detalles adicionales...')
                                            ->columnSpanFull()
                                            ->rows(4)
                                    ])->columns(2),

                                Section::make('Precios e Impuestos')
                                    ->description('Configuración de costos, precios de venta y detalles fiscales.')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        TextInput::make('purchase_cost')
                                            ->label('Costo de Compra')
                                            ->numeric()
                                            ->default(0.0000)
                                            ->prefix('$')
                                            ->step('0.01'),

                                        TextInput::make('sale_price')
                                            ->label('Precio de Venta')
                                            ->numeric()
                                            ->default(0.0000)
                                            ->prefix('$')
                                            ->step('0.01'),

                                        Select::make('tax_type')
                                            ->label('Tipo de Impuesto')
                                            ->options([
                                                'gravado' => 'Gravado',
                                                'exonerado' => 'Exonerado',
                                                'inafecto' => 'Inafecto',
                                                'exento' => 'Exento',
                                            ])
                                            ->default('gravado')
                                            ->required()
                                            ->native(false),

                                        TextInput::make('specific_taxes')
                                            ->label('Impuestos Específicos')
                                            ->placeholder('Ej. {"ILA": 15}')
                                            ->helperText('Formato JSON para impuestos adicionales.'),
                                    ])->columns(2),
                                Section::make('Imagenes')
                                    ->description('Imagenes del artículo.')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label('Imágenes del Artículo')
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
                                            ->maxFiles(5)
                                            ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                            ->visibility('public')
                                            ->directory(function (Get $get, ?\App\Models\Item $record) {
                                                $companyId = $get('company_id') ?? $record?->company_id ?? filament()->getTenant()?->id;
                                                
                                                if (empty($companyId)) {
                                                    return 'companies/temporary/items/tmp/images';
                                                }
                                                
                                                if ($record && $record->id) {
                                                    return "companies/company_{$companyId}/items/{$record->id}/images";
                                                }
                                                
                                                return "companies/company_{$companyId}/items/tmp/images";
                                            })
                                            ->deleteUploadedFileUsing(function (string $file, Get $get, ?\App\Models\Item $record) {
                                                $companyId = $get('company_id') ?? $record?->company_id ?? filament()->getTenant()?->id;
                                                
                                                $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';
                                                \Illuminate\Support\Facades\Storage::disk($disk)->delete($file);
                                            })
                                            ->columnSpanFull()
                                    ])
                            ])
                            ->columnSpan(['lg' => 2]),

                        Group::make()
                            ->schema([
                                Section::make('Organización')
                                    ->description('Clasificación en el sistema.')
                                    ->icon('heroicon-o-building-office')
                                    ->schema([
                                        Select::make('company_id')
                                            ->label('Empresa Perteneciente')
                                            ->relationship('company', 'name')
                                            ->native(false)
                                            ->required()
                                            ->live()
                                            ->visible(fn () => \Filament\Facades\Filament::getCurrentPanel()?->getId() === 'admin'),

                                        Select::make('type')
                                            ->label('Tipo de Artículo')
                                            ->options([
                                                'product' => 'Producto (Físico)',
                                                'service' => 'Servicio (Intangible)',
                                            ])
                                            ->default('product')
                                            ->live()
                                            ->required()
                                            ->native(false),

                                        Select::make('unit_measure_id')
                                            ->label('Unidad de Medida')
                                            ->relationship(
                                                name: 'unitMeasure',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: function ($query, Get $get) {
                                                    $companyId = $get('company_id') ?? filament()->getTenant()?->id;
                                                    if ($companyId) {
                                                        $query->where('company_id', $companyId);
                                                    } else {
                                                        $query->whereRaw('1 = 0'); // No results if no company selected
                                                    }
                                                }
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code} - {$record->name}")
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->required(),
                                    ]),

                                Section::make('Estado')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Artículo Activo')
                                            ->helperText('Los artículos inactivos no aparecerán en listas de ventas.')
                                            ->default(true),
                                    ]),
                                Section::make('Inventario')
                                    ->description('Gestión de stock actual y niveles mínimos para alertas.')
                                    ->icon('heroicon-o-archive-box')
                                    ->schema([
                                        Toggle::make('manage_stock')
                                            ->label('Controlar Existencias')
                                            ->helperText('Activa esta opción para llevar un registro de entradas y salidas.')
                                            ->default(true)
                                            ->live()
                                            ->columnSpanFull(),

                                        Grid::make(1)
                                            ->schema([
                                                TextInput::make('current_stock')
                                                    ->label('Stock Actual')
                                                    ->numeric()
                                                    ->default(0.00)
                                                    ->minValue(0)
                                                    ->step('0.01'),

                                                TextInput::make('minimum_stock')
                                                    ->label('Alerta de Stock Mínimo')
                                                    ->numeric()
                                                    ->default(0.00)
                                                    ->minValue(0)
                                                    ->step('0.01'),
                                            ])
                                            ->visible(fn (Get $get) => $get('manage_stock') === true),
                                    ])
                                    ->visible(fn (Get $get) => $get('type') === 'product'),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ]),
            ]);
    }
}
