<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make('Información Personal')
                        ->description('Datos básicos del usuario.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('name')
                                    ->label(__('users.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-user'),
                                
                                TextInput::make('email')
                                    ->label(__('users.fields.email'))
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-envelope'),
                            ]),
                        ]),

                    Section::make('Seguridad')
                        ->description('Contraseña y credenciales de acceso.')
                        ->icon('heroicon-o-lock-closed')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('password')
                                    ->label(__('users.fields.password'))
                                    ->password()
                                    ->maxLength(255)
                                    ->minLength(8)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->confirmed()
                                    ->prefixIcon('heroicon-m-key'),
                                
                                TextInput::make('password_confirmation')
                                    ->label('Confirmar Contraseña')
                                    ->password()
                                    ->maxLength(255)
                                    ->minLength(8)
                                    ->dehydrated(false)
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->prefixIcon('heroicon-m-key'),
                            ]),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Asignaciones y Accesos')
                        ->description('Empresas y roles del sistema.')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Select::make('company')
                                ->label(__('companies.title'))
                                ->relationship('company', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->prefixIcon('heroicon-m-building-office-2'),
                            
                            Select::make('roles')
                                ->label('Roles Administrativos')
                                ->relationship('roles', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->prefixIcon('heroicon-m-identification'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
    }
}
