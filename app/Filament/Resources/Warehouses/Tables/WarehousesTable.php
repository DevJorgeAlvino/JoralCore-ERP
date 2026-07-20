<?php

namespace App\Filament\Resources\Warehouses\Tables;

use Filament\Facades\Filament;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class WarehousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('name')
                    ->label('Nombre de Almacén')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_default')
                    ->label('Predeterminado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                ToggleColumn::make('is_active')
                    ->label('Activo'),

                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->sortable()
                    ->toggleable()
                    ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado'),
                TernaryFilter::make('is_default')
                    ->label('Predeterminado'),
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('code', 'asc');
    }
}
