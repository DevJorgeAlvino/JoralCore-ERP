<?php

namespace App\Filament\Resources\Items\RelationManagers;

use Filament\Facades\Filament;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoryMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'movements';

    protected static ?string $title = 'Kardex / Historial de Movimientos';

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('concept')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha / Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->sortable(),

                TextColumn::make('itemPresentation.name')
                    ->label('Presentación')
                    ->badge()
                    ->sortable(),

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
                ViewAction::make()
                    ->modalWidth('3xl')
                    ->infolist(function ($infolist) {
                        return \App\Filament\Resources\InventoryMovements\Schemas\InventoryMovementInfolist::configure($infolist);
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
