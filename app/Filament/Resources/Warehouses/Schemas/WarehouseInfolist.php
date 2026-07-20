<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WarehouseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información del Almacén')
                ->icon('heroicon-o-home-modern')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('name')
                            ->label('Nombre del Almacén')
                            ->weight('bold')
                            ->color('primary')
                            ->size('lg')
                            ->columnSpan(2),

                        TextEntry::make('code')
                            ->label('Código de Almacén')
                            ->badge(),

                        TextEntry::make('address')
                            ->label('Dirección')
                            ->placeholder('—')
                            ->columnSpan(2),

                        TextEntry::make('city')
                            ->label('Ciudad')
                            ->placeholder('—'),
                    ]),
                ])->columnSpanFull(),

            Section::make('Configuración y Estado')
                ->icon('heroicon-o-cog')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('is_default')
                            ->label('Predeterminado')
                            ->badge()
                            ->formatStateUsing(fn (bool $state) => $state ? 'Predeterminado' : 'No')
                            ->color(fn (bool $state) => $state ? 'warning' : 'gray'),

                        TextEntry::make('is_active')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (bool $state) => $state ? 'Activo' : 'Inactivo')
                            ->color(fn (bool $state) => $state ? 'success' : 'danger'),

                        TextEntry::make('company.name')
                            ->label('Empresa')
                            ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin'),
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
