<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Información de la Categoría')
                ->icon('heroicon-o-tag')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('name')
                            ->label('Nombre')
                            ->weight('bold')
                            ->color('primary')
                            ->size('lg'),

                        TextEntry::make('parent.name')
                            ->label('Categoría Padre')
                            ->placeholder('—'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('description')
                            ->label('Descripción')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ]),
                ])->columnSpanFull(),

            Section::make('Apariencia y Estado')
                ->icon('heroicon-o-paint-brush')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('icon')
                            ->label('Ícono')
                            ->icon(function ($state) {
                                if (empty($state)) {
                                    return null;
                                }
                                $iconName = str_starts_with($state, 'heroicon-') ? $state : "heroicon-o-{$state}";
                                try {
                                    app(\BladeUI\Icons\Factory::class)->svg($iconName);
                                    return $iconName;
                                } catch (\Throwable $e) {
                                    return null;
                                }
                            })
                            ->color('primary')
                            ->placeholder('—'),

                        ColorEntry::make('color')
                            ->label('Color'),

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
