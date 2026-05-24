<?php

namespace App\Filament\Company\Resources\UnitMeasures\Pages;

use App\Filament\Company\Resources\UnitMeasures\UnitMeasureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitMeasures extends ListRecords
{
    protected static string $resource = UnitMeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('lg')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['company_id'] = filament()->getTenant()?->id;
                    return $data;
                }),
        ];
    }
}
