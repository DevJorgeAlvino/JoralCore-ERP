<?php

namespace App\Filament\Resources\Items\Tables;

use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Actions\ForceDeleteBulkAction as ActionsForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction as ActionsRestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Item;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label(__('items.infolist.fields.sku'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('name')
                    ->label(__('items.table.item'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Item $record): string => Str::limit($record->description ?? '', 50)),

                TextColumn::make('type')
                    ->label(__('items.table.type'))
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
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('sale_price')
                    ->label(__('items.infolist.fields.sale_price'))
                    ->money()
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('current_stock')
                    ->label(__('items.table.stock'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (Item $record): string => 
                        $record->current_stock <= $record->minimum_stock ? 'danger' : 'success'
                    )
                    ->formatStateUsing(fn (Item $record): string => 
                        $record->manage_stock ? (string) $record->current_stock : 'N/A'
                    )
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('items.infolist.fields.is_active'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('barcode')
                    ->label(__('items.infolist.fields.barcode'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('purchase_cost')
                    ->label(__('items.infolist.fields.purchase_cost'))
                    ->money()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unitMeasure.name')
                    ->label(__('items.infolist.fields.unit_measure'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tax_type')
                    ->label(__('items.infolist.fields.tax_type'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('company.name')
                    ->label(__('items.infolist.fields.company'))
                    ->sortable()
                    ->toggleable()
                    ->visible(fn () => \Filament\Facades\Filament::getCurrentPanel()?->getId() === 'admin'),

                TextColumn::make('created_at')
                    ->label(__('items.infolist.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('items.infolist.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label(__('items.infolist.fields.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                ->native(false),
            ])
            ->bulkActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make(),
                    ActionsForceDeleteBulkAction::make(),
                    ActionsRestoreBulkAction::make(),
                ]),
            ]);
    }
}
