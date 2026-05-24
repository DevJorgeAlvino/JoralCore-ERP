<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
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
                Grid::make(3)->schema([
                    Group::make()->schema([
                        // ─── Información Comercial ───────────────────
                        Section::make(__('companies.sections.commercial'))
                            ->description(__('companies.sections.commercial_desc'))
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label(__('companies.fields.name'))
                                        ->required()
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-m-building-office')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                            if (blank($get('slug')) || $get('slug') === Str::slug($state)) {
                                                $set('slug', Str::slug($state));
                                            }
                                        }),

                                    TextInput::make('slug')
                                        ->label(__('companies.fields.slug'))
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->prefixIcon('heroicon-m-link')
                                        ->helperText('Identificador URL único. Se genera automáticamente.'),

                                    Textarea::make('description')
                                        ->label(__('companies.fields.description'))
                                        ->rows(2)
                                        ->maxLength(500)
                                        ->columnSpanFull(),
                                ]),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                    Group::make()->schema([
                        // ─── Estado de la Cuenta ─────────────────────
                        Section::make('Estado del Tenant')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                \Filament\Forms\Components\Toggle::make('is_active')
                                    ->label('Cuenta Activa')
                                    ->default(true)
                                    ->helperText('Permite el acceso de esta empresa al sistema ERP.'),
                            ]),

                        // ─── Localización y Configuración Regional ───
                        Section::make(__('companies.sections.localization'))
                            ->description('Moneda y zona horaria.')
                            ->icon('heroicon-o-globe-americas')
                            ->schema([
                                Select::make('country')
                                    ->label(__('companies.fields.country'))
                                    ->options([
                                        'PE' => '🇵🇪 Perú',
                                        'CL' => '🇨🇱 Chile',
                                    ])
                                    ->required()
                                    ->default('PE')
                                    ->native(false)
                                    ->prefixIcon('heroicon-m-flag')
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
                                    ->label(__('companies.fields.currency'))
                                    ->options([
                                        'PEN' => 'PEN — Sol peruano (S/)',
                                        'CLP' => 'CLP — Peso chileno ($)',
                                    ])
                                    ->required()
                                    ->default('PEN')
                                    ->prefixIcon('heroicon-m-currency-dollar')
                                    ->native(false),

                                Select::make('timezone')
                                    ->label(__('companies.fields.timezone'))
                                    ->options([
                                        'America/Lima'     => 'America/Lima (UTC-05:00)',
                                        'America/Santiago' => 'America/Santiago (UTC-03:00 / UTC-04:00)',
                                    ])
                                    ->required()
                                    ->default('America/Lima')
                                    ->prefixIcon('heroicon-m-clock')
                                    ->native(false)
                                    ->searchable(),
                            ]),
                    ])->columnSpan(['lg' => 1]),
                ]),

                // ─── Información Legal y Tributaria ──────────
                Section::make(__('companies.sections.legal'))
                    ->description(__('companies.sections.legal_desc'))
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('legal_name')
                                ->label(__('companies.fields.legal_name'))
                                ->required()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-m-scale')
                                ->columnSpan(2),

                            TextInput::make('identity_document')
                                ->label(fn (Get $get): string => $get('country') === 'CL' ? 'RUT (sin DV)' : 'RUC')
                                ->required()
                                ->numeric()
                                ->maxLength(20)
                                ->prefixIcon('heroicon-m-identification')
                                ->helperText(fn (Get $get): string => $get('country') === 'CL'
                                    ? 'Ingrese el RUT sin puntos, guión ni dígito verificador.'
                                    : 'Ingrese los 11 dígitos del RUC.'),

                            TextInput::make('dv')
                                ->label(__('companies.fields.dv'))
                                ->maxLength(1)
                                ->rule('regex:/^[0-9kK]$/')
                                ->helperText('Solo para RUT chileno. Acepta 0-9 o K.')
                                ->visible(fn (Get $get): bool => $get('country') === 'CL'),

                            TextInput::make('economic_activity_code')
                                ->label(__('companies.fields.economic_activity_code'))
                                ->maxLength(10)
                                ->prefixIcon('heroicon-m-briefcase')
                                ->helperText(fn (Get $get): string => $get('country') === 'CL'
                                    ? 'Código de actividad económica SII.'
                                    : 'Código CIIU registrado en SUNAT.'),

                            Select::make('tax_regime')
                                ->label('Régimen Tributario')
                                ->options(fn (Get $get) => $get('country') === 'CL' 
                                    ? ['propyme' => 'ProPyme', 'general' => 'Régimen General']
                                    : ['mype' => 'MYPE Tributario', 'especial' => 'Régimen Especial', 'general' => 'Régimen General']
                                )
                                ->native(false)
                                ->prefixIcon('heroicon-m-document-currency-dollar'),

                            \Filament\Forms\Components\Toggle::make('is_retention_agent')
                                ->label('Agente de Retención')
                                ->default(false)
                                ->inline(false)
                                ->helperText('Marca si la empresa retiene IGV/IVA.'),

                            TextInput::make('legal_rep_name')
                                ->label('Representante Legal (Nombre)')
                                ->maxLength(255)
                                ->prefixIcon('heroicon-m-user-circle')
                                ->columnSpan(2),

                            TextInput::make('legal_rep_document')
                                ->label('Representante Legal (Documento)')
                                ->maxLength(20)
                                ->prefixIcon('heroicon-m-identification'),
                        ]),
                    ]),

                // ─── Dirección Fiscal y Contacto ─────────────
                Section::make(__('companies.sections.contact'))
                    ->description(__('companies.sections.contact_desc'))
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('tax_address')
                            ->label(__('companies.fields.tax_address'))
                            ->required()
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Domicilio legal tal como aparece en el registro tributario.')
                            ->columnSpanFull(),

                        Grid::make(4)->schema([
                            TextInput::make('geo_code')
                                ->label(fn (Get $get): string => $get('country') === 'CL' ? 'Código de Comuna' : 'Ubigeo')
                                ->maxLength(10)
                                ->prefixIcon('heroicon-m-map')
                                ->helperText(fn (Get $get): string => $get('country') === 'CL'
                                    ? 'Código de comuna del SII.'
                                    : 'Código Ubigeo INEI.'),

                            TextInput::make('phone')
                                ->label(__('companies.fields.phone'))
                                ->tel()
                                ->maxLength(20)
                                ->prefixIcon('heroicon-m-phone')
                                ->helperText('Ej: +51 1 2345678'),

                            TextInput::make('email')
                                ->label(__('companies.fields.email'))
                                ->email()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-m-envelope'),

                            TextInput::make('website')
                                ->label('Sitio Web')
                                ->url()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-m-globe-alt')
                                ->placeholder('https://www.empresa.com'),
                        ]),
                    ]),
            ]);
    }
}
