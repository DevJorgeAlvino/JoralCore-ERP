<?php

namespace App\Filament\Company\Resources\UnitMeasures;

use App\Filament\Company\Resources\UnitMeasures\Pages\CreateUnitMeasure;
use App\Filament\Company\Resources\UnitMeasures\Pages\EditUnitMeasure;
use App\Filament\Company\Resources\UnitMeasures\Pages\ListUnitMeasures;
use App\Filament\Company\Resources\UnitMeasures\Pages\ViewUnitMeasure;
use App\Filament\Company\Resources\UnitMeasures\Schemas\UnitMeasureForm;
use App\Filament\Company\Resources\UnitMeasures\Schemas\UnitMeasureInfolist;
use App\Filament\Company\Resources\UnitMeasures\Tables\UnitMeasuresTable;
use App\Models\UnitMeasure;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitMeasureResource extends Resource
{
    protected static ?string $model = UnitMeasure::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'country'];
    }

    public static function getModelLabel(): string
    {
        return __('unit_measures.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('unit_measures.title');
    }

    public static function form(Schema $schema): Schema
    {
        return UnitMeasureForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UnitMeasureInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitMeasuresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUnitMeasures::route('/'),
            'view'   => ViewUnitMeasure::route('/{record}'),
            'edit'   => EditUnitMeasure::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', filament()->getTenant()?->id)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
