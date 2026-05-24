<?php

namespace App\Filament\Company\Resources\Users\Pages;

use App\Filament\Company\Resources\Users\UserResource;
use App\Filament\Company\Resources\Users\Widgets\UserStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('7xl'),
        ];
    }
}
