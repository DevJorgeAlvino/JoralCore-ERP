<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Models\Item;
use Filament\Facades\Filament;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('items.infolist.sections.main_info'))
                                    ->description(__('items.infolist.sections.main_info_desc'))
                                    ->icon('heroicon-o-cube')
                                    ->schema([

                                        TextEntry::make('name')
                                            ->label(__('items.infolist.fields.name'))
                                            ->weight('bold'),
                                        TextEntry::make('sku')
                                            ->label(__('items.infolist.fields.sku'))
                                            ->badge(),
                                        TextEntry::make('barcode')
                                            ->label(__('items.infolist.fields.barcode'))
                                            ->placeholder('-'),
                                        TextEntry::make('slug')
                                            ->label(__('items.infolist.fields.slug'))
                                            ->color('gray'),
                                        TextEntry::make('description')
                                            ->label(__('items.infolist.fields.description'))
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make(__('items.infolist.sections.prices_taxes'))
                                    ->description(__('items.infolist.sections.prices_taxes_desc'))
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        TextEntry::make('purchase_cost')
                                            ->label(__('items.infolist.fields.purchase_cost'))
                                            ->money(),
                                        TextEntry::make('sale_price')
                                            ->label(__('items.infolist.fields.sale_price'))
                                            ->money()
                                            ->weight('bold')
                                            ->color('success'),
                                        TextEntry::make('tax_type')
                                            ->label(__('items.infolist.fields.tax_type'))
                                            ->badge(),
                                        TextEntry::make('specific_taxes')
                                            ->label(__('items.infolist.fields.specific_taxes'))
                                            ->placeholder('-'),
                                    ])->columns(2),

                                Section::make(__('items.infolist.sections.gallery'))
                                    ->description(__('items.infolist.sections.gallery_desc'))
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        ImageEntry::make('images')
                                            ->hiddenLabel()
                                            ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                            ->limit(10)
                                            ->columnSpanFull()
                                            ->visible(fn (Item $record) => ! empty($record->images)),

                                        TextEntry::make('no_images_placeholder')
                                            ->hiddenLabel()
                                            ->default(__('items.infolist.fields.no_images'))
                                            ->color('gray')
                                            ->icon('heroicon-m-exclamation-circle')
                                            ->visible(fn (Item $record) => empty($record->images)),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),

                        Group::make()
                            ->schema([
                                Section::make(__('items.infolist.sections.organization'))
                                    ->description(__('items.infolist.sections.organization_desc'))
                                    ->icon('heroicon-o-building-office')
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->label(__('items.infolist.fields.company'))
                                            ->visible(fn () => Filament::getTenant() === null),
                                        TextEntry::make('type')
                                            ->label(__('items.infolist.fields.item_type'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'product' => 'success',
                                                'service' => 'info',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'product' => __('items.infolist.fields.product'),
                                                'service' => __('items.infolist.fields.service'),
                                                default => $state,
                                            }),
                                        TextEntry::make('unitMeasure.name')
                                            ->label(__('items.infolist.fields.unit_measure')),
                                        IconEntry::make('is_active')
                                            ->label(__('items.infolist.fields.is_active'))
                                            ->boolean(),
                                    ]),
                                Section::make(__('items.infolist.sections.inventory'))
                                    ->description(__('items.infolist.sections.inventory_desc'))
                                    ->icon('heroicon-o-archive-box')
                                    ->schema([
                                        IconEntry::make('manage_stock')
                                            ->label(__('items.infolist.fields.manage_stock'))
                                            ->columnSpan(2)
                                            ->boolean(),
                                        TextEntry::make('current_stock')
                                            ->label(__('items.infolist.fields.current_stock'))
                                            ->numeric()
                                            ->badge()
                                            ->color(fn ($state, Item $record) => $state <= $record->minimum_stock ? 'danger' : 'success'),
                                        TextEntry::make('minimum_stock')
                                            ->label(__('items.infolist.fields.minimum_stock'))
                                            ->numeric(),
                                    ])->columns(2)
                                    ->visible(fn (Item $record) => $record->type === 'product'),
                                Section::make(__('items.infolist.sections.metadata'))
                                    ->description(__('items.infolist.sections.metadata_desc'))
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        TextEntry::make('id')
                                            ->label(__('items.infolist.fields.internal_id'))
                                            ->color('gray')
                                            ->copyable(),
                                        TextEntry::make('created_at')
                                            ->label(__('items.infolist.fields.created_at'))
                                            ->dateTime()
                                            ->placeholder('-'),
                                        TextEntry::make('updated_at')
                                            ->label(__('items.infolist.fields.updated_at'))
                                            ->dateTime()
                                            ->placeholder('-'),
                                        TextEntry::make('deleted_at')
                                            ->label(__('items.infolist.fields.deleted_at'))
                                            ->dateTime()
                                            ->color('danger')
                                            ->visible(fn (Item $record): bool => $record->trashed()),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ]),
            ]);
    }
}
