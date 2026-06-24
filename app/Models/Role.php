<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'guard_name',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'guard_name' => 'string',
            'name' => 'string',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',            // Nombre de la relación morfica
            'model_has_roles',  // La tabla real de Spatie
            'role_id',          // Foreign key del rol
            'model_id'          // Foreign key del usuario
        );
    }
}
