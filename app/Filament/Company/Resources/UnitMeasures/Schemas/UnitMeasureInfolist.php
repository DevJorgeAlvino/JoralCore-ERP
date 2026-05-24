<?php

namespace App\Filament\Company\Resources\UnitMeasures\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UnitMeasureInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('unit_measures.sections.general'))
                    ->description(__('unit_measures.sections.general_desc'))
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('code')
                                ->label(__('unit_measures.fields.code'))
                                ->icon('heroicon-o-hashtag')
                                ->weight('bold')
                                ->color('primary'),
                            TextEntry::make('name')
                                ->label(__('unit_measures.fields.name'))
                                ->icon('heroicon-o-beaker'),
                            TextEntry::make('country')
                                ->label(__('unit_measures.fields.country'))
                                ->icon('heroicon-o-globe-alt')
                                ->placeholder('-')
                                ->columnSpanFull(),
                        ]),
                    ])->collapsible()->columnSpanFull(),
                Section::make('Auditoría')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('created_at')
                                ->label(__('unit_measures.fields.created_at'))
                                ->dateTime()
                                ->placeholder('-'),
                            TextEntry::make('updated_at')
                                ->label(__('unit_measures.fields.updated_at'))
                                ->dateTime()
                                ->placeholder('-'),
                        ]),
                    ])->collapsed()->columnSpanFull(),
            ]);
    }
}
