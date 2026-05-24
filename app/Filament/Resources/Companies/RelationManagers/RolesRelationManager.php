<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $relatedResource = RoleResource::class;
    
    protected static ?string $title = 'Roles de la Empresa';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre del Rol')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-identification')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guard_name')
                    ->label('Guardia')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-m-shield-check'),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Usuarios Asignados')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-users'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Crear Rol')
                    ->schema(fn (CreateAction $action): array => [
                        TextInput::make('name')
                            ->label('Nombre del Rol')
                            ->required()
                            ->prefixIcon('heroicon-m-identification'),
                        TextInput::make('guard_name')
                            ->label('Guard (Sistema)')
                            ->default('web')
                            ->required()
                            ->disabled()
                            ->prefixIcon('heroicon-m-shield-check'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
