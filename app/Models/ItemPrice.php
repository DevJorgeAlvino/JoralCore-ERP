<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPrice extends Model
{
    use HasUlids;

    protected $fillable = [
        'item_presentation_id',
        'price_list_name',
        'purchase_cost',
        'sale_price',
        'currency',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'purchase_cost' => 'decimal:4',
        'sale_price' => 'decimal:4',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function presentation(): BelongsTo
    {
        return $this->belongsTo(ItemPresentation::class, 'item_presentation_id');
    }
}
