<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Item extends Model
{
    use SoftDeletes, HasUlids;

    protected $fillable = [
        'company_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'description',
        'type',
        'unit_code',
        'purchase_cost',
        'sale_price',
        'tax_type',
        'specific_taxes',
        'manage_stock',
        'current_stock',
        'minimum_stock',
        'is_active',
        'images',
    ];

    protected $casts = [
        'specific_taxes' => 'array',
        'manage_stock' => 'boolean',
        'is_active' => 'boolean',
        'purchase_cost' => 'decimal:4',
        'sale_price' => 'decimal:4',
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'images' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });

        static::updating(function ($item) {
            if ($item->isDirty('name') && empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'unit_code', 'code');
    }
}
