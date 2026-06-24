<?php

namespace App\Filament\Resources\Brands\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->width(48)
                    ->height(48)
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=B&background=6366f1&color=fff&size=48')
                    ->rounded(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('website')
                    ->label('Sitio Web')
                    ->url(fn ($record) => $record->website)
                    ->openUrlInNewTab()
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('items_count')
                    ->label('Ítems')
                    ->counts('items')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin'),

                ToggleColumn::make('is_active')
                    ->label('Activo'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Estado'),
            ])
            ->recordAction(ViewAction::class)
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->striped();
    }
}
