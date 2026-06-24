<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemPresentation extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'item_id',
        'unit_measure_id',
        'name',
        'barcode',
        'conversion_factor',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:4',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(ItemStock::class);
    }

    /**
     * Get the active/current price for a given list. Defaults to 'General'.
     */
    public function activePrice(string $list = 'General'): ?ItemPrice
    {
        return $this->prices()
            ->where('price_list_name', $list)
            ->where('is_active', true)
            ->latest()
            ->first();
    }
}
