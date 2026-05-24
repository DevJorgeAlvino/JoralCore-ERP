<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $relatedResource = UserResource::class;
    
    protected static ?string $title = 'Usuarios Asignados';

    public function table(Table $table): Table
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
                    ->label('Usuario')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->email),

                TextColumn::make('rolesAll.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->separator(',')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->icon(fn ($state) => $state ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-circle')
                    ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No')
                    ->toggleable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->label('Vincular Usuario'),
                CreateAction::make()
                    ->label('Crear Usuario')
                    ->schema(fn (CreateAction $action): array => [
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-user'),
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-envelope'),
                                TextInput::make('password')
                                    ->label('Contraseña')
                                    ->required()
                                    ->password()
                                    ->maxLength(255)
                                    ->minLength(8)
                                    ->confirmed()
                                    ->prefixIcon('heroicon-m-key'),
                                TextInput::make('password_confirmation')
                                    ->label('Confirmar Contraseña')
                                    ->required()
                                    ->password()
                                    ->maxLength(255)
                                    ->minLength(8)
                                    ->prefixIcon('heroicon-m-key'),
                                Select::make('role_id')
                                    ->label('Rol en la Empresa')
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
                                    ->required()
                                    ->columnSpanFull()
                                    ->prefixIcon('heroicon-m-identification'),
                            ])
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
