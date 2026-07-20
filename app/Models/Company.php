<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasUlids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'legal_name',
        'identity_document',
        'dv',
        'economic_activity_code',
        'description',
        'country',
        'currency',
        'timezone',
        'tax_address',
        'geo_code',
        'phone',
        'email',
        'is_active',
        'tax_regime',
        'website',
        'legal_rep_name',
        'legal_rep_document',
        'is_retention_agent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ──────────────────────────────────────────────
    // Accessors & Mutators
    // ──────────────────────────────────────────────

    /**
     * Country code siempre se almacena en mayúsculas (PE, CL).
     */
    protected function country(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? strtoupper($value) : null,
            set: fn (?string $value) => $value ? strtoupper(trim($value)) : null,
        );
    }

    /**
     * Currency code siempre se almacena en mayúsculas (PEN, CLP).
     */
    protected function currency(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? strtoupper($value) : null,
            set: fn (?string $value) => $value ? strtoupper(trim($value)) : null,
        );
    }

    /**
     * Dígito verificador: almacenar en mayúscula (para la 'K' del RUT chileno).
     */
    protected function dv(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? strtoupper($value) : null,
            set: fn (?string $value) => $value ? strtoupper(trim($value)) : null,
        );
    }

    /**
     * Identity document: eliminar guiones, puntos y espacios al guardar.
     */
    protected function identityDocument(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value
                ? preg_replace('/[\s.\-]/', '', trim($value))
                : null,
        );
    }

    /**
     * Formato presentable del documento tributario.
     * PE: RUC tal cual | CL: RUT con guión + DV (ej: 76.XXX.XXX-K)
     */
    protected function formattedDocument(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->identity_document) {
                    return null;
                }

                if ($this->country === 'CL' && $this->dv !== null) {
                    return $this->identity_document.'-'.$this->dv;
                }

                return $this->identity_document;
            },
        );
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Verifica si la empresa opera en Perú.
     */
    public function isPeruvian(): bool
    {
        return $this->country === 'PE';
    }

    /**
     * Verifica si la empresa opera en Chile.
     */
    public function isChilean(): bool
    {
        return $this->country === 'CL';
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // public function categories()
    // {
    //     return $this->hasMany(Category::class);
    // }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function settings()
    {
        return $this->hasMany(CompanySetting::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
}
