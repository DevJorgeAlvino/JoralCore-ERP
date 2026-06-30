<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([

                // ── Left (main) ─────────────────────────────────────────────
                Group::make()->columnSpan(['lg' => 2])->schema([

                    Section::make('Información del Contacto')
                        ->description('Ingresa los datos del cliente, proveedor o ambos.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('name')
                                    ->label('Nombre o Razón Social')
                                    ->placeholder('Ej. Juan Pérez / Distribuidora Joral S.A.C.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Select::make('type')
                                    ->label('Tipo de Contacto')
                                    ->options([
                                        'customer' => 'Cliente',
                                        'supplier' => 'Proveedor',
                                        'both' => 'Ambos (Cliente / Proveedor)',
                                    ])
                                    ->required()
                                    ->default('customer')
                                    ->native(false),

                                Select::make('document_type')
                                    ->label('Tipo de Documento')
                                    ->options([
                                        'dni' => 'Perú: DNI',
                                        'ruc' => 'Perú: RUC',
                                        'run' => 'Chile: RUN',
                                        'rut' => 'Chile: RUT',
                                        'passport' => 'Pasaporte',
                                        'foreign_id' => 'Cédula Extranjería',
                                        'other' => 'Otro',
                                    ])
                                    ->required()
                                    ->live()
                                    ->native(false),

                                TextInput::make('document_number')
                                    ->label('Número de Documento')
                                    ->placeholder('Ingresa el número...')
                                    ->required()
                                    ->maxLength(50)
                                    ->live(onBlur: true)
                                    ->unique(
                                        table: 'contacts',
                                        column: 'document_number',
                                        ignoreRecord: true,
                                        modifyRuleUsing: function (Unique $rule, Get $get) {
                                            $companyId = Filament::getCurrentPanel()?->getId() === 'admin'
                                                ? $get('company_id')
                                                : Filament::getTenant()?->id;
                                            
                                            $docType = $get('document_type');
                                            
                                            return $rule->where('company_id', $companyId)
                                                        ->where('document_type', $docType);
                                        }
                                    )
                                    ->rules([
                                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $docType = $get('document_type');
                                            
                                            if ($docType === 'dni') {
                                                if (!preg_match('/^[0-9]{8}$/', $value)) {
                                                    $fail('El DNI debe tener exactamente 8 números.');
                                                }
                                            }
                                            
                                            if ($docType === 'ruc') {
                                                if (!preg_match('/^(10|15|17|20)[0-9]{9}$/', $value)) {
                                                    $fail('El RUC debe tener 11 números y comenzar con 10, 15, 17 o 20.');
                                                }
                                            }

                                            if ($docType === 'run' || $docType === 'rut') {
                                                $cleanRun = strtolower(preg_replace('/[^0-9kK]/', '', $value));
                                                if (strlen($cleanRun) < 8 || strlen($cleanRun) > 9) {
                                                    $fail('El RUN/RUT debe tener entre 8 y 9 dígitos (ej: 12345678-k).');
                                                    return;
                                                }
                                                
                                                $num = substr($cleanRun, 0, -1);
                                                $dv = substr($cleanRun, -1);
                                                
                                                $sum = 0;
                                                $mul = 2;
                                                for ($i = strlen($num) - 1; $i >= 0; $i--) {
                                                    $sum += $num[$i] * $mul;
                                                    $mul = ($mul === 7) ? 2 : $mul + 1;
                                                }
                                                $res = 11 - ($sum % 11);
                                                $calcDv = ($res === 11) ? '0' : (($res === 10) ? 'k' : (string)$res);
                                                
                                                if ($dv !== $calcDv) {
                                                    $fail('El RUN/RUT ingresado es inválido (Dígito verificador incorrecto).');
                                                }
                                            }
                                        },
                                    ])
                                    ->columnSpanFull(),

                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->placeholder('ejemplo@correo.com')
                                    ->email()
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label('Teléfono / Celular')
                                    ->placeholder('Ej. +51 987654321')
                                    ->tel()
                                    ->maxLength(50),
                            ])
                        ]),

                    Section::make('Direcciones de Despacho')
                        ->description('Gestiona las direcciones de entrega de este contacto.')
                        ->icon('heroicon-o-truck')
                        ->schema([
                            Repeater::make('addresses')
                                ->relationship('addresses')
                                ->schema([
                                    TextInput::make('label')
                                        ->label('Etiqueta')
                                        ->placeholder('Ej. Oficina Central, Almacén Callao')
                                        ->required()
                                        ->default('Oficina Principal'),

                                    TextInput::make('address')
                                        ->label('Dirección')
                                        ->placeholder('Dirección completa (Calle, Av., Nro)')
                                        ->required()
                                        ->columnSpanFull(),

                                    TextInput::make('city')
                                        ->label('Ciudad / Distrito')
                                        ->placeholder('Ej. Miraflores o Providencia')
                                        ->required(),

                                    TextInput::make('state_region')
                                        ->label('Región / Departamento')
                                        ->placeholder('Ej. Lima o Santiago')
                                        ->required(),

                                    Select::make('country')
                                        ->label('País')
                                        ->options([
                                            'PE' => 'Perú',
                                            'CL' => 'Chile',
                                            'US' => 'Estados Unidos',
                                            'AR' => 'Argentina',
                                            'CO' => 'Colombia',
                                            'MX' => 'México',
                                        ])
                                        ->required()
                                        ->default('PE')
                                        ->native(false),

                                    Toggle::make('is_default')
                                        ->label('Dirección por Defecto')
                                        ->default(false)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                ->defaultItems(1)
                                ->grid(1)
                                ->columnSpanFull(),
                        ]),
                ]),

                // ── Right (settings/billing) ─────────────────────────────────
                Group::make()->columnSpan(['lg' => 1])->schema([

                    Section::make('Facturación')
                        ->description('Condiciones comerciales y tributarias.')
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            Select::make('company_id')
                                ->label('Empresa')
                                ->relationship('company', 'name')
                                ->native(false)
                                ->required()
                                ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin')
                                ->live(),

                            Select::make('billing_payment_terms')
                                ->label('Plazo de Pago')
                                ->options([
                                    'cash' => 'Contado (Efectivo / Transferencia)',
                                    'credit_15' => 'Crédito 15 días',
                                    'credit_30' => 'Crédito 30 días',
                                    'credit_60' => 'Crédito 60 días',
                                    'credit_90' => 'Crédito 90 días',
                                ])
                                ->required()
                                ->default('cash')
                                ->native(false),
                        ]),

                    Section::make('Estado')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Contacto Activo')
                                ->helperText('Los contactos inactivos no se pueden seleccionar en nuevas transacciones.')
                                ->default(true),
                        ]),
                ]),
            ]),
        ]);
    }
}
