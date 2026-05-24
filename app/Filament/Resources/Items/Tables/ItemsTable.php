<?php

namespace App\Filament\Resources\Items\Tables;

use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Actions\ForceDeleteBulkAction as ActionsForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction as ActionsRestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('gray')
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Artículo')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => Str::limit($record->description ?? '', 40))
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipo')
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
                    })
                    ->sortable(),

                TextColumn::make('sale_price')
                    ->label('Precio Venta')
                    ->money()
                    ->sortable()
                    ->color('success')
                    ->weight('bold')
                    ->alignment('right'),

                TextColumn::make('current_stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->alignment('right')
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($record->type === 'service' || !$record->manage_stock) return 'gray';
                        return $state <= $record->minimum_stock ? 'danger' : 'success';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'service' || !$record->manage_stock) return '-';
                        return $state;
                    }),

                ToggleColumn::make('is_active')
                    ->label('Activo')
                    ->sortable(),

                // Columnas ocultas por defecto
                TextColumn::make('barcode')
                    ->label('Código de Barras')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('purchase_cost')
                    ->label('Costo')
                    ->money()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unit_code')
                    ->label('Und. Medida')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tax_type')
                    ->label('Impuesto')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Eliminado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                ->native(false),
            ])
            ->bulkActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make(),
                    ActionsForceDeleteBulkAction::make(),
                    ActionsRestoreBulkAction::make(),
                ]),
            ]);
    }
}
