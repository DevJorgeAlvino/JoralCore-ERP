<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditCompanyProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Perfil de Empresa';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Información Comercial (editable) ────────
                Section::make('Información Comercial')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Comercial')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ─── Información Tributaria (solo lectura) ───
                // Estos campos solo pueden ser modificados por un
                // Super Admin desde el panel administrativo central.
                Section::make('Información Legal y Tributaria')
                    ->description('Solo el administrador global puede modificar estos datos.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('legal_name')
                            ->label('Razón Social')
                            ->disabled(),

                        TextInput::make('identity_document')
                            ->label('RUC / RUT')
                            ->disabled(),

                        TextInput::make('dv')
                            ->label('DV')
                            ->disabled()
                            ->visible(fn ($record) => $record?->country === 'CL'),

                        TextInput::make('country')
                            ->label('País')
                            ->disabled(),

                        TextInput::make('currency')
                            ->label('Moneda')
                            ->disabled(),

                        TextInput::make('timezone')
                            ->label('Zona Horaria')
                            ->disabled(),

                        TextInput::make('economic_activity_code')
                            ->label('Código Actividad Económica')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // ─── Dirección y Contacto (editable) ─────────
                Section::make('Dirección Fiscal y Contacto')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Textarea::make('tax_address')
                            ->label('Dirección Fiscal')
                            ->required()
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        TextInput::make('geo_code')
                            ->label(fn ($record) => $record?->country === 'CL' ? 'Código de Comuna' : 'Ubigeo')
                            ->maxLength(10),

                        TextInput::make('phone')
                            ->label('Teléfono Corporativo')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label('Correo Corporativo')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}