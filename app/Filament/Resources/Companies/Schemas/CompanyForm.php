<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Información Comercial ───────────────────
                Section::make('Información Comercial')
                    ->description('Datos de identificación interna de la empresa.')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Comercial')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (blank($get('slug')) || $get('slug') === Str::slug($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Identificador URL único. Se genera automáticamente.'),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ─── Localización y Configuración Regional ───
                Section::make('Localización y Configuración Regional')
                    ->description('País de operación, moneda y zona horaria.')
                    ->icon('heroicon-o-globe-americas')
                    ->schema([
                        Select::make('country')
                            ->label('País')
                            ->options([
                                'PE' => '🇵🇪 Perú',
                                'CL' => '🇨🇱 Chile',
                            ])
                            ->required()
                            ->default('PE')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state === 'PE') {
                                    $set('currency', 'PEN');
                                    $set('timezone', 'America/Lima');
                                }

                                if ($state === 'CL') {
                                    $set('currency', 'CLP');
                                    $set('timezone', 'America/Santiago');
                                }
                            }),

                        Select::make('currency')
                            ->label('Moneda Base')
                            ->options([
                                'PEN' => 'PEN — Sol peruano (S/)',
                                'CLP' => 'CLP — Peso chileno ($)',
                            ])
                            ->required()
                            ->default('PEN')
                            ->native(false),

                        Select::make('timezone')
                            ->label('Zona Horaria')
                            ->options([
                                'America/Lima'     => 'America/Lima (UTC-05:00)',
                                'America/Santiago' => 'America/Santiago (UTC-03:00 / UTC-04:00)',
                            ])
                            ->required()
                            ->default('America/Lima')
                            ->native(false)
                            ->searchable(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // ─── Información Legal y Tributaria ──────────
                Section::make('Información Legal y Tributaria')
                    ->description('Razón social y documentos tributarios para facturación electrónica.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextInput::make('legal_name')
                            ->label('Razón Social')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('identity_document')
                            ->label(fn (Get $get): string => $get('country') === 'CL' ? 'RUT (sin DV)' : 'RUC')
                            ->required()
                            ->numeric()
                            ->maxLength(20)
                            ->helperText(fn (Get $get): string => $get('country') === 'CL'
                                ? 'Ingrese el RUT sin puntos, guión ni dígito verificador.'
                                : 'Ingrese los 11 dígitos del RUC.'),

                        TextInput::make('dv')
                            ->label('Dígito Verificador')
                            ->maxLength(1)
                            ->rule('regex:/^[0-9kK]$/')
                            ->helperText('Solo para RUT chileno. Acepta 0-9 o K.')
                            ->visible(fn (Get $get): bool => $get('country') === 'CL'),

                        TextInput::make('economic_activity_code')
                            ->label('Código Actividad Económica')
                            ->maxLength(10)
                            ->helperText(fn (Get $get): string => $get('country') === 'CL'
                                ? 'Código de actividad económica SII.'
                                : 'Código CIIU registrado en SUNAT.'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // ─── Dirección Fiscal y Contacto ─────────────
                Section::make('Dirección Fiscal y Contacto')
                    ->description('Domicilio legal y datos de contacto corporativo.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Textarea::make('tax_address')
                            ->label('Dirección Fiscal')
                            ->required()
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Domicilio legal tal como aparece en el registro tributario.')
                            ->columnSpanFull(),

                        TextInput::make('geo_code')
                            ->label(fn (Get $get): string => $get('country') === 'CL' ? 'Código de Comuna' : 'Ubigeo')
                            ->maxLength(10)
                            ->helperText(fn (Get $get): string => $get('country') === 'CL'
                                ? 'Código de comuna del SII (5 dígitos).'
                                : 'Código Ubigeo INEI (6 dígitos).'),

                        TextInput::make('phone')
                            ->label('Teléfono Corporativo')
                            ->tel()
                            ->maxLength(20)
                            ->helperText('Incluir código de país. Ej: +51 1 2345678'),

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
