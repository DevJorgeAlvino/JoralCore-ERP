<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Role;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Components\Utilities\Set as UtilitiesSet;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'rolesAll';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $relatedResource = RoleResource::class;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('roles.assigned');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('roles.table.role'))
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-identification')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('roles.table.company'))
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-building-office-2')
                    ->default(__('roles.table.global_level')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->schema(fn (CreateAction $action): array => [
                        Select::make('company_id')
                            ->relationship(
                                name: 'company',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, $livewire) {
                                    $companyIds = $livewire->getOwnerRecord()->companies()->pluck('companies.id')->toArray();

                                    return $query->whereIn('id', $companyIds);
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => Filament::getTenant()?->id)
                            ->hidden(fn () => Filament::getTenant() !== null),
                        TextInput::make('name')
                            ->label(__('users.fields.name'))
                            ->required(),
                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required(),
                    ]),
                Action::make('asignar_rol_contextual')
                    ->label(__('roles.actions.assign_role'))
                    ->icon('heroicon-m-link')
                    ->color('primary')
                    ->modalWidth('md')
                    ->form(function () {
                        $tenant = Filament::getTenant();

                        return [
                            // 1. PRIMER SELECT: La Empresa
                            Select::make('company_id')
                                ->label(__('companies.single'))
                                ->placeholder(__('roles.actions.select_company_first'))
                                ->searchable()
                                ->preload()
                                ->options(function ($livewire) {
                                    return $livewire->getOwnerRecord()
                                        ->companies()
                                        ->pluck('companies.name', 'companies.id');
                                })
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (UtilitiesSet $set) => $set('role_id', null))
                                ->default($tenant?->id)
                                ->hidden($tenant !== null),

                            // 2. SEGUNDO SELECT: El Rol (Dependiente)
                            Select::make('role_id')
                                ->label(__('roles.actions.available_role'))
                                ->placeholder(__('roles.actions.select_role'))
                                ->searchable()
                                ->preload()
                                ->hidden(fn (UtilitiesGet $get) => ! $get('company_id'))
                                ->options(function (UtilitiesGet $get, $livewire) {
                                    $companyId = $get('company_id');
                                    if (! $companyId) {
                                        return [];
                                    }

                                    $user = $livewire->getOwnerRecord();
                                    $rolesYaAsignados = $user->rolesAll()->pluck('roles.id')->toArray();

                                    return Role::query()
                                        ->where('company_id', $companyId)
                                        ->where('name', '!=', 'super_admin')
                                        ->whereNotIn('roles.id', $rolesYaAsignados)
                                        ->pluck('name', 'id');
                                })
                                ->required(),
                        ];
                    })
                    ->action(function (array $data, $livewire) {
                        $user = $livewire->getOwnerRecord();

                        // 1. Configuramos el contexto de Spatie
                        setPermissionsTeamId($data['company_id']);

                        // 2. Buscamos el rol
                        $role = Role::find($data['role_id']);

                        // 3. Asignamos
                        if ($role) {
                            $user->assignRole($role);

                            Notification::make()
                                ->title(__('roles.actions.assigned_success'))
                                ->success()
                                ->send();
                        }

                        // 4. Limpiamos el contexto
                        setPermissionsTeamId(null);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
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
