<?php

namespace App\Filament\Company\Resources\Users\RelationManagers;

use App\Models\Role;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Components\Utilities\Set as UtilitiesSet;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'rolesAll';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        $tenant = filament()->getTenant();

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Rol Asignado')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-identification')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label('Empresa (Contexto)')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-building-office-2')
                    ->default('Nivel Global'),
            ])
            ->headerActions([
                Action::make('asignar_rol')
                    ->label('Asignar Rol')
                    ->icon('heroicon-m-link')
                    ->color('primary')
                    ->modalWidth('md')
                    ->form([
                        Select::make('role_id')
                            ->label('Rol Disponible')
                            ->placeholder('Selecciona un rol')
                            ->searchable()
                            ->preload()
                            ->options(function ($livewire) use ($tenant) {
                                $user = $livewire->getOwnerRecord();
                                $rolesYaAsignados = $user->rolesAll()->pluck('roles.id')->toArray();

                                // Solo roles de esta empresa, sin super_admin
                                return Role::query()
                                    ->where('company_id', $tenant?->id)
                                    ->where('name', '!=', 'super_admin')
                                    ->whereNotIn('roles.id', $rolesYaAsignados)
                                    ->pluck('name', 'id');
                            })
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) use ($tenant) {
                        $user = $livewire->getOwnerRecord();

                        setPermissionsTeamId($tenant?->id);

                        $role = Role::find($data['role_id']);

                        if ($role) {
                            $user->assignRole($role);

                            \Filament\Notifications\Notification::make()
                                ->title('Rol asignado correctamente')
                                ->success()
                                ->send();
                        }

                        setPermissionsTeamId(null);
                    }),
            ])
            ->recordActions([
                DetachAction::make(),
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
