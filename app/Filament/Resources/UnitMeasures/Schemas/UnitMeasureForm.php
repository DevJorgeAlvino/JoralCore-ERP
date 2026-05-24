<?php

namespace App\Filament\Resources\UnitMeasures\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UnitMeasureForm
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
                            TextInput::make('code')
                                ->label(__('unit_measures.fields.code'))
                                ->required()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-o-hashtag'),
                            TextInput::make('name')
                                ->label(__('unit_measures.fields.name'))
                                ->required()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-o-beaker'),
                            TextInput::make('country')
                                ->label(__('unit_measures.fields.country'))
                                ->maxLength(255)
                                ->prefixIcon('heroicon-o-globe-alt')
                                ->columnSpanFull(),
                        ]),
                    ])->collapsible()->columnSpanFull(),
            ]);
    }
}
