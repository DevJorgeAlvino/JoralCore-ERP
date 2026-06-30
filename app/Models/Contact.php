<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'company_id',
        'type',
        'legal_type',
        'name',
        'document_type',
        'document_number',
        'business_activity',
        'email',
        'phone',
        'billing_payment_terms',
        'credit_limit',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:4',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(ContactAddress::class);
    }

    public function defaultAddress(): ?ContactAddress
    {
        return $this->addresses()->where('is_default', true)->first() 
            ?? $this->addresses()->first();
    }
}
