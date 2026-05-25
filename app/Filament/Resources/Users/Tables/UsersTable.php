<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=09090b')
                    ->circular()
                    ->size(36)
                    ->grow(false),

                TextColumn::make('name')
                    ->label(__('users.table.user'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->email),

                TextColumn::make('company.name')
                    ->label(__('users.table.assigned_companies'))
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-building-office-2')
                    ->separator(',')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('roles.name')
                    ->label(__('users.table.global_roles'))
                    ->badge()
                    ->color('primary')
                    ->separator(',')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email_verified_at')
                    ->label(__('users.table.verified'))
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->icon(fn ($state) => $state ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-circle')
                    ->formatStateUsing(fn ($state) => $state ? __('users.table.yes') : __('users.table.no'))
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('users.table.registered'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
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
