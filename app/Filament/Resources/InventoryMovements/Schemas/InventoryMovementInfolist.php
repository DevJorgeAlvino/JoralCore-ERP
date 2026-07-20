<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryMovementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detalle del Movimiento de Inventario')
                ->icon('heroicon-o-arrows-right-left')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('itemPresentation.item.name')
                            ->label('Producto')
                            ->weight('bold')
                            ->color('primary')
                            ->size('lg')
                            ->columnSpan(2),

                        TextEntry::make('itemPresentation.name')
                            ->label('Presentación')
                            ->badge(),

                        TextEntry::make('warehouse.name')
                            ->label('Almacén'),

                        TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'in' => 'success',
                                'out' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'in' => 'Entrada (+)',
                                'out' => 'Salida (-)',
                                default => $state,
                            }),

                        TextEntry::make('concept')
                            ->label('Concepto')
                            ->badge(),

                        TextEntry::make('quantity')
                            ->label('Cantidad')
                            ->numeric(decimalPlaces: 4),

                        TextEntry::make('unit_cost')
                            ->label('Costo Unitario')
                            ->money(fn () => Filament::getTenant()?->currency ?? 'PEN'),

                        TextEntry::make('balance_stock')
                            ->label('Saldo Resultante (Stock)')
                            ->numeric(decimalPlaces: 4)
                            ->weight('bold')
                            ->color('primary'),
                    ]),
                ])->columnSpanFull(),

            Section::make('Referencia y Auditoría')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('reference_document')
                            ->label('Documento de Respaldo')
                            ->placeholder('—'),

                        TextEntry::make('reference_number')
                            ->label('Número de Referencia')
                            ->placeholder('—'),

                        TextEntry::make('user.name')
                            ->label('Registrado Por'),

                        TextEntry::make('created_at')
                            ->label('Fecha y Hora de Registro')
                            ->dateTime('d/m/Y, H:i:s'),

                        TextEntry::make('company.name')
                            ->label('Empresa')
                            ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin'),
                    ]),
                ])->columnSpanFull(),
        ]);
    }
}
