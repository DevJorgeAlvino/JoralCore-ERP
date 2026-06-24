<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemStock extends Model
{
    use HasUlids;

    protected $fillable = [
        'item_presentation_id',
        'company_id',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
    ];

    protected $casts = [
        'current_stock' => 'decimal:4',
        'minimum_stock' => 'decimal:4',
        'maximum_stock' => 'decimal:4',
    ];

    public function presentation(): BelongsTo
    {
        return $this->belongsTo(ItemPresentation::class, 'item_presentation_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isBelowMinimum(): bool
    {
        return $this->current_stock < $this->minimum_stock;
    }
}
