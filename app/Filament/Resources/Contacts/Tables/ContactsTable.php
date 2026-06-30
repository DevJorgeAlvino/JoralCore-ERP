<?php

namespace App\Filament\Resources\Contacts\Tables;

use Filament\Facades\Filament;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre / Razón Social')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->email),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'customer' => 'info',
                        'supplier' => 'warning',
                        'both' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'customer' => 'Cliente',
                        'supplier' => 'Proveedor',
                        'both' => 'Ambos',
                        default => $state,
                    }),

                TextColumn::make('document_type')
                    ->label('Doc. Identidad')
                    ->formatStateUsing(fn ($record) => strtoupper($record->document_type) . ': ' . $record->document_number)
                    ->searchable(['document_number']),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->toggleable(),

                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->sortable()
                    ->toggleable()
                    ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin'),

                ToggleColumn::make('is_active')
                    ->label('Activo'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado'),
                SelectFilter::make('type')
                    ->label('Tipo de Contacto')
                    ->options([
                        'customer' => 'Cliente',
                        'supplier' => 'Proveedor',
                        'both' => 'Ambos',
                    ]),
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
            ->defaultSort('name', 'asc')
            ->striped();
    }
}
