<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasUlids, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Warehouse $warehouse) {
            if ($warehouse->is_default) {
                static::where('company_id', $warehouse->company_id)
                    ->where('id', '!=', $warehouse->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'address',
        'city',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ItemStock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
