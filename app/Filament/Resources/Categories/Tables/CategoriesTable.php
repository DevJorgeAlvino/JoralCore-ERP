<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->width('40px'),

                IconColumn::make('icon')
                    ->label('')
                    ->width('40px')
                    ->icon(function ($state) {
                        if (empty($state)) {
                            return null;
                        }
                        $iconName = str_starts_with($state, 'heroicon-') ? $state : "heroicon-o-{$state}";
                        try {
                            app(\BladeUI\Icons\Factory::class)->svg($iconName);
                            return $iconName;
                        } catch (\Throwable $e) {
                            return null;
                        }
                    }),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->parent?->name ? '↳ '.$record->parent->name : null),

                TextColumn::make('children_count')
                    ->label('Subcategorías')
                    ->counts('children')
                    ->badge()
                    ->color('gray'),

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

                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('parent_id')
                    ->label('Categoría Padre')
                    ->relationship('parent', 'name')
                    ->placeholder('Todas'),
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
