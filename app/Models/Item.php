<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'company_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'description',
        'type',
        'category_id',
        'brand_id',
        'unit_measure_id',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class);
    }

    public function presentations(): HasMany
    {
        return $this->hasMany(ItemPresentation::class);
    }

    public function defaultPresentation(): ?ItemPresentation
    {
        return $this->presentations()->where('is_default', true)->first();
    }
}
