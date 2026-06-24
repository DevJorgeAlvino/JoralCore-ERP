<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Información de la Marca')
                ->icon('heroicon-o-building-storefront')
                ->schema([
                    Grid::make(3)->schema([
                        ImageEntry::make('logo')
                            ->label('Logo')
                            ->width(80)
                            ->height(80)
                            ->defaultImageUrl('https://ui-avatars.com/api/?name=B&background=6366f1&color=fff&size=80'),

                        Group::make()->columnSpan(2)->schema([
                            TextEntry::make('name')
                                ->label('Nombre')
                                ->weight('bold')
                                ->color('primary')
                                ->size('lg'),

                            TextEntry::make('website')
                                ->label('Sitio Web')
                                ->url(fn ($state) => $state)
                                ->openUrlInNewTab()
                                ->placeholder('—'),

                            TextEntry::make('slug')
                                ->label('Slug')
                                ->badge()
                                ->color('gray'),
                        ]),
                    ]),
                ])->columnSpanFull(),

            Section::make('Estado y Auditoría')
                ->icon('heroicon-o-clock')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('is_active')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (bool $state) => $state ? 'Activo' : 'Inactivo')
                            ->color(fn (bool $state) => $state ? 'success' : 'danger'),

                        TextEntry::make('created_at')->label('Creado')->dateTime('d/m/Y, H:i:s'),
                        TextEntry::make('updated_at')->label('Actualizado')->dateTime('d/m/Y, H:i:s'),
                    ]),
                ])->columnSpanFull(),
        ]);
    }
}
