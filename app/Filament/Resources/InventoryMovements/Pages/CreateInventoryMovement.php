<?php

namespace App\Filament\Resources\InventoryMovements\Pages;

use App\Filament\Resources\InventoryMovements\InventoryMovementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryMovement extends CreateRecord
{
    protected static string $resource = InventoryMovementResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $itemStock = \App\Models\ItemStock::firstOrCreate([
                'warehouse_id' => $data['warehouse_id'],
                'item_presentation_id' => $data['item_presentation_id'],
            ], [
                'current_stock' => 0.0000,
                'minimum_stock' => 0.0000,
            ]);

            $quantity = (float) $data['quantity'];
            $newBalance = $data['type'] === 'in'
                ? $itemStock->current_stock + $quantity
                : $itemStock->current_stock - $quantity;

            $itemStock->update([
                'current_stock' => $newBalance,
            ]);

            $data['balance_stock'] = $newBalance;

            return parent::handleRecordCreation($data);
        });
    }
}
