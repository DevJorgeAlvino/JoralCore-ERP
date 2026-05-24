<?php

namespace App\Filament\Resources\UnitMeasures\Pages;

use App\Filament\Resources\UnitMeasures\UnitMeasureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitMeasures extends ListRecords
{
    protected static string $resource = UnitMeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
