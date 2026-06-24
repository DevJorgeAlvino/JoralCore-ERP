<?php

namespace App\Filament\Resources\Items;

use App\Filament\Resources\Items\Pages\EditItem;
use App\Filament\Resources\Items\Pages\ListItems;
use App\Filament\Resources\Items\Pages\ViewItem;
use App\Filament\Resources\Items\RelationManagers\PresentationsRelationManager;
use App\Filament\Resources\Items\Schemas\ItemForm;
use App\Filament\Resources\Items\Schemas\ItemInfolist;
use App\Filament\Resources\Items\Tables\ItemsTable;
use App\Models\Item;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = true;

    protected static string|\UnitEnum|null $navigationGroup = 'Catálogo';

    protected static ?int $navigationSort = 3;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku', 'barcode', 'presentations.name', 'presentations.barcode'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        $details = [];

        if (\Filament\Facades\Filament::getCurrentPanel()?->getId() === 'admin' && $record->company) {
            $details['Empresa'] = $record->company->name;
        }

        if ($record->sku) {
            $details['SKU'] = $record->sku;
        }

        if ($record->barcode) {
            $details['Código de Barras'] = $record->barcode;
        }

        if ($record->description) {
            $details['Descripción'] = $record->description;
        }

        // Mostrar las presentaciones asociadas en el resultado de búsqueda
        $presentationsList = $record->presentations->pluck('name')->implode(', ');
        if ($presentationsList) {
            $details['Presentaciones'] = $presentationsList;
        }

        return $details;
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['presentations', 'company']);
    }

    public static function getModelLabel(): string
    {
        return __('items.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('items.title');
    }

    public static function form(Schema $schema): Schema
    {
        return ItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PresentationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItems::route('/'),
            'view' => ViewItem::route('/{record}'),
            'edit' => EditItem::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
