<?php

namespace App\Filament\Resources\InventoryMovements\Tables;

use Filament\Facades\Filament;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoryMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha / Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->sortable(),

                TextColumn::make('itemPresentation.item.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->itemPresentation?->name),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Entrada (+)',
                        'out' => 'Salida (-)',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('concept')
                    ->label('Concepto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Cant.')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('unit_cost')
                    ->label('Costo U.')
                    ->money(fn () => Filament::getTenant()?->currency ?? 'PEN')
                    ->sortable(),

                TextColumn::make('balance_stock')
                    ->label('Saldo (Stock)')
                    ->numeric(decimalPlaces: 2)
                    ->weight('bold')
                    ->color('primary')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label('Almacén')
                    ->relationship('warehouse', 'name', fn ($query) => $query->where('company_id', Filament::getTenant()?->id)),
                
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'in' => 'Entradas (+)',
                        'out' => 'Salidas (-)',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
