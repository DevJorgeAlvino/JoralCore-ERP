<?php

namespace App\Filament\Resources\UnitMeasures\Pages;

use App\Filament\Resources\UnitMeasures\UnitMeasureResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUnitMeasure extends ViewRecord
{
    protected static string $resource = UnitMeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
