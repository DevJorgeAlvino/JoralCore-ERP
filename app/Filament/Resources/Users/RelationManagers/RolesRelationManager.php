<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Companies\Tables\CompaniesTable;
use App\Filament\Resources\Roles\RoleResource;
use App\Models\Company;
use App\Models\Role;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Components\Utilities\Set as UtilitiesSet;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'rolesAll';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $relatedResource = RoleResource::class;

    public function table(Table $table): Table
    {
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
                CreateAction::make()
                ->schema(fn (CreateAction $action): array => [
                        Select::make('company_id')
                         ->relationship(
                            name: 'company', 
                            titleAttribute: 'name',
                            modifyQueryUsing: function (Builder $query, $livewire) {
                                $companyIds = $livewire->getOwnerRecord()->company()->pluck('companies.id')->toArray();
                                return $query->whereIn('id', $companyIds);
                            }
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required(),
                    ]),
               Action::make('asignar_rol_contextual')
                    ->label('Asignar Rol')
                    ->icon('heroicon-m-link')
                    ->color('primary')
                    ->modalWidth('md')
                    ->form([
                        
                        // 1. PRIMER SELECT: La Empresa
                        Select::make('company_id')
                            ->label('Empresa')
                            ->placeholder('Selecciona la empresa primero')
                            ->searchable()
                            ->preload() // Seguro porque un usuario raramente tiene miles de empresas asignadas
                            ->options(function ($livewire) {
                                // Traemos solo las empresas asignadas al usuario que editamos
                                return $livewire->getOwnerRecord()
                                    ->company() // O ->companies() según tu modelo
                                    ->pluck('companies.name', 'companies.id');
                            })
                            ->required()
                            ->live() // ⚡️ ESTO ES VITAL: Avisa al formulario cuando cambia
                            ->afterStateUpdated(fn (UtilitiesSet $set) => $set('role_id', null)), // Limpia el rol si cambias de empresa

                        // 2. SEGUNDO SELECT: El Rol (Dependiente)
                        Select::make('role_id')
                            ->label('Rol Disponibles')
                            ->placeholder('Selecciona un rol')
                            ->searchable()
                            ->preload() // Aquí sí es seguro usar preload porque serán pocos roles (ej. 10 o 20)
                            
                            // 👇 MAGIA: Solo se muestra si ya elegiste empresa
                            ->hidden(fn (UtilitiesGet $get) => ! $get('company_id'))
                            
                            // 👇 MAGIA 2: Carga solo los roles de ESA empresa
                            ->options(function (UtilitiesGet $get, $livewire) {
                                $companyId = $get('company_id');
                                if (! $companyId) return [];

                                // Opcional: Filtramos roles que YA tiene asignados para no repetir
                                $user = $livewire->getOwnerRecord();
                                $rolesYaAsignados = $user->rolesAll()->pluck('roles.id')->toArray();

                                return Role::query()
                                    ->where('company_id', $companyId) // Solo de esta empresa
                                    ->whereNotIn('roles.id', $rolesYaAsignados) // Que no tenga ya
                                    ->pluck('name', 'id');
                            })
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $user = $livewire->getOwnerRecord();
                        
                        // 1. Configuramos el contexto de Spatie
                        setPermissionsTeamId($data['company_id']);

                        // 2. Buscamos el rol
                        $role = Role::find($data['role_id']);

                        // 3. Asignamos
                        if ($role) {
                            $user->assignRole($role);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Rol asignado correctamente')
                                ->success()
                                ->send();
                        }

                        // 4. Limpiamos el contexto
                        setPermissionsTeamId(null);
                    })
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DetachAction::make()
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
