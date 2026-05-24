<?php

namespace App\Filament\Resources\UnitMeasures\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitMeasureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('country'),
            ]);
    }
}
