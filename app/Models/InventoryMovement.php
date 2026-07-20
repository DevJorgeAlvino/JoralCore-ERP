<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasUlids;

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'item_presentation_id',
        'user_id',
        'type',
        'concept',
        'quantity',
        'unit_cost',
        'balance_stock',
        'reference_document',
        'reference_number',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'balance_stock' => 'decimal:4',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function itemPresentation(): BelongsTo
    {
        return $this->belongsTo(ItemPresentation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
