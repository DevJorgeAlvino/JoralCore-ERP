<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Companies\CompanyResource;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';

    protected static ?string $relatedResource = CompanyResource::class;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('companies.relations.assigned');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('companies.fields.name'))
                    ->weight('bold')
                    ->icon('heroicon-m-building-office-2')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('companies.fields.slug'))
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-m-link'),

                TextColumn::make('is_active')
                    ->label(__('companies.table.status'))
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? __('companies.table.active') : __('companies.table.inactive'))
                    ->icon(fn ($state) => $state ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->label(__('companies.relations.assign_company')),
            ])
            ->recordActions([
                ViewAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
