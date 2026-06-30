<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Facades\Filament;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Información del Contacto')
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('name')
                            ->label('Nombre / Razón Social')
                            ->weight('bold')
                            ->color('primary')
                            ->size('lg')
                            ->columnSpan(2),

                        TextEntry::make('type')
                            ->label('Tipo de Contacto')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'customer' => 'info',
                                'supplier' => 'warning',
                                'both' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'customer' => 'Cliente',
                                'supplier' => 'Proveedor',
                                'both' => 'Ambos',
                                default => $state,
                            }),

                        TextEntry::make('document_type')
                            ->label('Tipo de Documento')
                            ->formatStateUsing(fn ($state) => strtoupper($state)),

                        TextEntry::make('document_number')
                            ->label('Número de Documento'),

                        TextEntry::make('email')
                            ->label('Correo Electrónico')
                            ->placeholder('—'),

                        TextEntry::make('phone')
                            ->label('Teléfono / Celular')
                            ->placeholder('—'),
                    ]),
                ])->columnSpanFull(),

            Section::make('Direcciones de Despacho')
                ->icon('heroicon-o-truck')
                ->schema([
                    RepeatableEntry::make('addresses')
                        ->label('')
                        ->schema([
                            Grid::make(5)->schema([
                                TextEntry::make('label')
                                    ->label('Etiqueta')
                                    ->weight('bold')
                                    ->color('primary'),

                                TextEntry::make('address')
                                    ->label('Dirección')
                                    ->columnSpan(2),

                                TextEntry::make('city')
                                    ->label('Ciudad / Región')
                                    ->formatStateUsing(fn ($record) => $record->city . ' / ' . $record->state_region),

                                IconEntry::make('is_default')
                                    ->label('Por Defecto')
                                    ->boolean(),
                            ]),
                        ])
                        ->placeholder('No hay direcciones de despacho registradas para este contacto.')
                        ->columnSpanFull(),
                ])->columnSpanFull(),

            Section::make('Facturación y Estado')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('billing_payment_terms')
                            ->label('Condición de Pago')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cash' => 'Contado (Efectivo / Transferencia)',
                                'credit_15' => 'Crédito 15 días',
                                'credit_30' => 'Crédito 30 días',
                                'credit_60' => 'Crédito 60 días',
                                'credit_90' => 'Crédito 90 días',
                                default => $state,
                            }),

                        TextEntry::make('company.name')
                            ->label('Empresa')
                            ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin'),

                        TextEntry::make('is_active')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (bool $state) => $state ? 'Activo' : 'Inactivo')
                            ->color(fn (bool $state) => $state ? 'success' : 'danger'),
                    ]),
                ])->columnSpanFull(),

            Section::make('Auditoría')
                ->icon('heroicon-o-clock')
                ->collapsed()
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('created_at')->label('Creado')->dateTime('d/m/Y, H:i:s'),
                        TextEntry::make('updated_at')->label('Actualizado')->dateTime('d/m/Y, H:i:s'),
                    ]),
                ])->columnSpanFull(),
        ]);
    }
}
