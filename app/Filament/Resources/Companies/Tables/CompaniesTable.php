<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=09090b')
                    ->circular()
                    ->size(36)
                    ->grow(false),

                TextColumn::make('name')
                    ->label(__('companies.fields.name'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->slug),

                TextColumn::make('country')
                    ->label(__('companies.fields.country'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'PE' => '🇵🇪 Perú',
                        'CL' => '🇨🇱 Chile',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'PE' => 'danger',
                        'CL' => 'info',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency')
                    ->label(__('companies.fields.currency'))
                    ->badge()
                    ->icon('heroicon-m-currency-dollar')
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('website')
                    ->label('Sitio Web')
                    ->icon('heroicon-m-globe-alt')
                    ->url(fn ($record): ?string => $record->website)
                    ->color('primary')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->icon(fn ($state) => $state ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                    ->formatStateUsing(fn ($state) => $state ? 'Activa' : 'Inactiva')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('companies.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label(__('companies.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('deleted_at')
                    ->label(__('companies.fields.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
