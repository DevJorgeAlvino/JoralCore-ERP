<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                // Select::make('company_id')
                //     ->label('Empresa')
                //     ->relationship('company', 'name')
                //     ->required()
                //     ->preload()
                //     ->multiple()
                //     ->createOptionForm([
                //         TextInput::make('name')
                //             ->required()
                //             ->maxLength(255),
                //         TextInput::make('slug')
                //             ->required()
                //             ->maxLength(255),
                //         TextInput::make('description')
                //             ->maxLength(255),
                //     ]),
                // Select::make('roles')
                //         ->label('Roles')
                //         ->relationship('roles', 'name')
                //         ->multiple()
                //         ->preload()
                //         ->searchable()
                //         ->required(),
            ]);
    }
}
