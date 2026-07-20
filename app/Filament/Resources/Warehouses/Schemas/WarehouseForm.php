<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([

                // ── Left (main) ─────────────────────────────────────────────
                Group::make()->columnSpan(['lg' => 2])->schema([

                    Section::make('Información del Almacén')
                        ->description('Ingresa los detalles físicos y lógicos de tu bodega.')
                        ->icon('heroicon-o-home-modern')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('name')
                                    ->label('Nombre del Almacén')
                                    ->placeholder('Ej. Almacén Central, Bodega Norte')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('code')
                                    ->label('Código del Almacén')
                                    ->placeholder('Ej. ALM01, BOD02')
                                    ->required()
                                    ->maxLength(50)
                                    ->live(onBlur: true)
                                    ->unique(
                                        table: 'warehouses',
                                        column: 'code',
                                        ignoreRecord: true,
                                        modifyRuleUsing: function (Unique $rule, Get $get) {
                                            $companyId = Filament::getCurrentPanel()?->getId() === 'admin'
                                                ? $get('company_id')
                                                : Filament::getTenant()?->id;
                                            
                                            return $rule->where('company_id', $companyId);
                                        }
                                    ),

                                TextInput::make('address')
                                    ->label('Dirección')
                                    ->placeholder('Ej. Av. De las Américas 123')
                                    ->columnSpanFull()
                                    ->maxLength(255),

                                TextInput::make('city')
                                    ->label('Ciudad')
                                    ->placeholder('Ej. Lima o Santiago')
                                    ->maxLength(255),
                            ])
                        ]),
                ]),

                // ── Right (settings) ─────────────────────────────────────────
                Group::make()->columnSpan(['lg' => 1])->schema([

                    Section::make('Configuración')
                        ->schema([
                            Select::make('company_id')
                                ->label('Empresa')
                                ->relationship('company', 'name')
                                ->native(false)
                                ->required()
                                ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin')
                                ->live(),

                            Toggle::make('is_default')
                                ->label('Almacén Predeterminado')
                                ->helperText('Se usará por defecto en las transacciones de inventario.')
                                ->default(false),

                            Toggle::make('is_active')
                                ->label('Almacén Activo')
                                ->default(true),
                        ]),
                ]),
            ]),
        ]);
    }
}
