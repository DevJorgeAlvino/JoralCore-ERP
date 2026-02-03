<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                ->schema(fn (CreateAction $action): array => [
                    Grid::make()
                        ->columns(2)
                        ->schema([
                             TextInput::make('name')
                                ->label('Nombre')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('Correo Electrónico')
                                ->required()
                                ->email()
                                ->maxLength(255),
                             TextInput::make('password')
                                ->label('Contraseña')
                                ->required()
                                ->password()
                                ->maxLength(255)
                                ->minLength(8)
                                ->confirmed(),
                            TextInput::make('password_confirmation')
                                ->label('Confirmar Contraseña')
                                ->required()
                                ->password()
                                ->maxLength(255)
                                ->minLength(8),
                            Select::make('role_id')
                                ->relationship(
                                    name: 'rolesAll', 
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function (Builder $query, $livewire) {
                                        return $livewire->getOwnerRecord()->roles();
                                    }
                                )
                                ->saveRelationshipsUsing(function (Model $record, $state, $livewire) {
                                    $company = $livewire->getOwnerRecord();
                                    setPermissionsTeamId($company->id);
                                    $record->syncRoles($state);
                                    setPermissionsTeamId(null);
                                })
                                ->dehydrated(false)
                                ->searchable()
                                ->preload()
                                ->multiple()
                                ->required(),
                        ])
                    ]),
            ]);
    }
}
