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

        static::created(function ($item) {
            // 1. Obtener el almacén principal por defecto de la empresa
            $defaultWarehouseId = \App\Models\Warehouse::where('company_id', $item->company_id)
                ->where('is_default', true)
                ->value('id')
                ?? \App\Models\Warehouse::where('company_id', $item->company_id)
                ->value('id');

            // 2. Crear la presentación base por defecto
            $baseName = $item->type === 'service' ? 'Servicio Base' : 'Unidad';

            $presentation = $item->presentations()->create([
                'unit_measure_id' => $item->unit_measure_id,
                'name' => $baseName,
                'conversion_factor' => 1.0000,
                'is_default' => true,
                'is_active' => true,
            ]);

            // 3. Crear precio general por defecto
            if (($item->purchase_cost ?? 0) > 0 || ($item->sale_price ?? 0) > 0) {
                $presentation->prices()->create([
                    'price_list_name' => 'General',
                    'purchase_cost' => $item->purchase_cost ?? 0,
                    'sale_price' => $item->sale_price ?? 0,
                    'currency' => $item->company->currency ?? 'PEN',
                    'is_active' => true,
                ]);
            }

            // 4. Crear stock inicial en el almacén por defecto
            if ($defaultWarehouseId && $item->type === 'product') {
                $initialStock = $item->current_stock ?? 0;
                
                $presentation->stocks()->create([
                    'warehouse_id' => $defaultWarehouseId,
                    'current_stock' => $initialStock,
                    'minimum_stock' => $item->minimum_stock ?? 0,
                ]);

                if ($initialStock > 0) {
                    $userId = auth()->id() 
                        ?? \App\Models\User::whereHas('companies', function($q) use ($item) {
                            $q->where('companies.id', $item->company_id);
                        })->value('id')
                        ?? \App\Models\User::value('id');

                    if ($userId) {
                        \App\Models\InventoryMovement::create([
                            'company_id' => $item->company_id,
                            'warehouse_id' => $defaultWarehouseId,
                            'item_presentation_id' => $presentation->id,
                            'user_id' => $userId,
                            'type' => 'in',
                            'concept' => 'Inventario Inicial',
                            'quantity' => $initialStock,
                            'unit_cost' => $item->purchase_cost ?? 0,
                            'balance_stock' => $initialStock,
                            'reference_document' => 'system_init',
                            'reference_number' => 'REG-' . $item->sku,
                        ]);
                    }
                }
            }
        });

        static::updating(function ($item) {
            if ($item->isDirty('name') && empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });

        static::updated(function ($item) {
            $defaultPresentation = $item->defaultPresentation();

            if ($defaultPresentation) {
                // Actualizar precio de la presentación por defecto
                $defaultPresentation->prices()->updateOrCreate(
                    [
                        'price_list_name' => 'General',
                        'is_active' => true,
                    ],
                    [
                        'purchase_cost' => $item->purchase_cost ?? 0,
                        'sale_price' => $item->sale_price ?? 0,
                        'currency' => $item->company->currency ?? 'PEN',
                    ]
                );

                // Actualizar stock en el almacén por defecto y registrar Kardex si cambia
                $defaultWarehouseId = \App\Models\Warehouse::where('company_id', $item->company_id)
                    ->where('is_default', true)
                    ->value('id')
                    ?? \App\Models\Warehouse::where('company_id', $item->company_id)
                    ->value('id');

                if ($defaultWarehouseId && $item->type === 'product') {
                    $oldStockRecord = $defaultPresentation->stocks()->where('warehouse_id', $defaultWarehouseId)->first();
                    $oldStock = $oldStockRecord ? (float) $oldStockRecord->current_stock : 0.0000;
                    $newStock = (float) ($item->current_stock ?? 0.0000);

                    if ($newStock != $oldStock) {
                        $diff = $newStock - $oldStock;
                        $type = $diff > 0 ? 'in' : 'out';
                        $quantity = abs($diff);

                        $defaultPresentation->stocks()->updateOrCreate(
                            [
                                'warehouse_id' => $defaultWarehouseId,
                            ],
                            [
                                'current_stock' => $newStock,
                                'minimum_stock' => $item->minimum_stock ?? 0,
                            ]
                        );

                        $userId = auth()->id() 
                            ?? \App\Models\User::whereHas('companies', function($q) use ($item) {
                                $q->where('companies.id', $item->company_id);
                            })->value('id')
                            ?? \App\Models\User::value('id');

                        if ($userId) {
                            \App\Models\InventoryMovement::create([
                                'company_id' => $item->company_id,
                                'warehouse_id' => $defaultWarehouseId,
                                'item_presentation_id' => $defaultPresentation->id,
                                'user_id' => $userId,
                                'type' => $type,
                                'concept' => 'Ajuste por Edición',
                                'quantity' => $quantity,
                                'unit_cost' => $item->purchase_cost ?? 0,
                                'balance_stock' => $newStock,
                                'reference_document' => 'manual_edit',
                                'reference_number' => 'EDIT-' . $item->sku,
                            ]);
                        }
                    } else {
                        // Solo actualizar límites de stock si el valor numérico no cambió
                        $defaultPresentation->stocks()->updateOrCreate(
                            [
                                'warehouse_id' => $defaultWarehouseId,
                            ],
                            [
                                'minimum_stock' => $item->minimum_stock ?? 0,
                            ]
                        );
                    }
                }
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

    public function movements(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(InventoryMovement::class, ItemPresentation::class);
    }

    public function defaultPresentation(): ?ItemPresentation
    {
        return $this->presentations()->where('is_default', true)->first();
    }

    public function totalStock(): float
    {
        if (!$this->manage_stock) {
            return 0.00;
        }

        return (float) \App\Models\ItemStock::whereIn(
            'item_presentation_id',
            $this->presentations()->pluck('id')
        )->sum('current_stock');
    }

    public function totalMinimumStock(): float
    {
        if (!$this->manage_stock) {
            return 0.00;
        }

        return (float) \App\Models\ItemStock::whereIn(
            'item_presentation_id',
            $this->presentations()->pluck('id')
        )->sum('minimum_stock');
    }
}
