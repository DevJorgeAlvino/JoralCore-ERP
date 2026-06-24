<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CompanyPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    CompanyPanelProvider::class,
];
