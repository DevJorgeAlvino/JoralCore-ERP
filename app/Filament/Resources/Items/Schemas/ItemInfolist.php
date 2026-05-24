<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Models\Item;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ItemInfolist
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

                                        TextEntry::make('name')
                                            ->label('Nombre')
                                            ->weight('bold'),
                                        TextEntry::make('sku')
                                            ->label('SKU')
                                            ->badge(),
                                        TextEntry::make('barcode')
                                            ->label('Código de Barras')
                                            ->placeholder('-'),
                                        TextEntry::make('slug')
                                            ->label('Slug')
                                            ->color('gray'),
                                        TextEntry::make('description')
                                            ->label('Descripción')
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Precios e Impuestos')
                                    ->description('Configuración de costos, precios de venta y detalles fiscales.')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        TextEntry::make('purchase_cost')
                                            ->label('Costo de Compra')
                                            ->money(),
                                        TextEntry::make('sale_price')
                                            ->label('Precio de Venta')
                                            ->money()
                                            ->weight('bold')
                                            ->color('success'),
                                        TextEntry::make('tax_type')
                                            ->label('Tipo de Impuesto')
                                            ->badge(),
                                        TextEntry::make('specific_taxes')
                                            ->label('Impuestos Específicos')
                                            ->placeholder('-'),
                                    ])->columns(2),

                                Section::make('Galería de Imágenes')
                                    ->description('Fotos y material visual del artículo.')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        ImageEntry::make('images')
                                            ->hiddenLabel()
                                            ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                            // ->height(150)
                                            // ->extraImgAttributes([
                                            //     'class' => 'rounded-lg shadow-sm',
                                            //     'style' => 'object-fit: cover;',
                                            // ])
                                            ->limit(10)
                                            ->columnSpanFull()
                                            ->visible(fn (Item $record) => !empty($record->images)),
                                            
                                        TextEntry::make('no_images_placeholder')
                                            ->hiddenLabel()
                                            ->default('No se ha subido ninguna imagen para este artículo.')
                                            ->color('gray')
                                            ->icon('heroicon-m-exclamation-circle')
                                            ->visible(fn (Item $record) => empty($record->images)),
                                    ])
                            ])
                            ->columnSpan(['lg' => 2]),

                        Group::make()
                            ->schema([
                                Section::make('Organización')
                                    ->description('Clasificación y disponibilidad en el sistema.')
                                    ->icon('heroicon-o-building-office')
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->label('Empresa')
                                            ->visible(fn () => \Filament\Facades\Filament::getTenant() === null),
                                        TextEntry::make('type')
                                            ->label('Tipo de Artículo')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'product' => 'success',
                                                'service' => 'info',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'product' => 'Producto',
                                                'service' => 'Servicio',
                                                default => $state,
                                            }),
                                        TextEntry::make('unitMeasure.name')
                                            ->label('Unidad de Medida'),
                                        IconEntry::make('is_active')
                                            ->label('Activo')
                                            ->boolean(),
                                    ]),
                                Section::make('Inventario')
                                    ->description('Gestión de stock actual y niveles mínimos para alertas.')
                                    ->icon('heroicon-o-archive-box')
                                    ->schema([
                                        IconEntry::make('manage_stock')
                                            ->label('Controla Stock')
                                            ->columnSpan(2)
                                            ->boolean(),
                                        TextEntry::make('current_stock')
                                            ->label('Stock Actual')
                                            ->numeric()
                                            ->badge()
                                            ->color(fn ($state, Item $record) => $state <= $record->minimum_stock ? 'danger' : 'success'),
                                        TextEntry::make('minimum_stock')
                                            ->label('Stock Mínimo')
                                            ->numeric(),
                                    ])->columns(2)
                                    ->visible(fn (Item $record) => $record->type === 'product'),
                                Section::make('Metadatos')
                                    ->description('Registro de auditoría.')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        TextEntry::make('id')
                                            ->label('ID Interno')
                                            ->color('gray')
                                            ->copyable(),
                                        TextEntry::make('created_at')
                                            ->label('Creación')
                                            ->dateTime()
                                            ->placeholder('-'),
                                        TextEntry::make('updated_at')
                                            ->label('Última Actualización')
                                            ->dateTime()
                                            ->placeholder('-'),
                                        TextEntry::make('deleted_at')
                                            ->label('Eliminado el')
                                            ->dateTime()
                                            ->color('danger')
                                            ->visible(fn (Item $record): bool => $record->trashed()),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ]),
            ]);
    }
}
