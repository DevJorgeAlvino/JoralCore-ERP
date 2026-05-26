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
use Illuminate\Database\Eloquent\Model;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $relatedResource = RoleResource::class;
    
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('companies.relations.roles');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('roles.table.role'))
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-identification')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guard_name')
                    ->label(__('roles.table.guard'))
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-m-shield-check'),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label(__('roles.table.assigned_users'))
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-users'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('companies.relations.create_role'))
                    ->schema(fn (CreateAction $action): array => [
                        TextInput::make('name')
                            ->label(__('roles.table.role'))
                            ->required()
                            ->prefixIcon('heroicon-m-identification'),
                        TextInput::make('guard_name')
                            ->label(__('roles.table.guard'))
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
