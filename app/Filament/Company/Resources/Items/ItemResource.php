<?php

namespace App\Filament\Company\Resources\Items;

use App\Filament\Company\Resources\Items\Pages\CreateItem;
use App\Filament\Company\Resources\Items\Pages\EditItem;
use App\Filament\Company\Resources\Items\Pages\ListItems;
use App\Filament\Company\Resources\Items\Pages\ViewItem;
use App\Filament\Company\Resources\Items\Schemas\ItemForm;
use App\Filament\Company\Resources\Items\Schemas\ItemInfolist;
use App\Filament\Company\Resources\Items\Tables\ItemsTable;
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku', 'barcode'];
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListItems::route('/'),
            'create' => CreateItem::route('/create'),
            'view'   => ViewItem::route('/{record}'),
            'edit'   => EditItem::route('/{record}/edit'),
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
