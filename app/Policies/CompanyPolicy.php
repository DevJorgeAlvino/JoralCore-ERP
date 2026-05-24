<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Company;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Verifica si el usuario pertenece a la empresa indicada.
     * Los super_admin (company_id = null) no necesitan esta verificación
     * porque acceden desde el panel Admin central.
     */
    private function belongsToCompany(AuthUser $authUser, Company $company): bool
    {
        // Si el usuario tiene el rol super_admin global, siempre tiene acceso
        if ($authUser->roles()->withoutGlobalScopes()->where('name', 'super_admin')->exists()) {
            return true;
        }

        // Para usuarios regulares, verificar pertenencia al tenant
        return $authUser->company()->whereKey($company)->exists();
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Company');
    }

    public function view(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('View:Company')
            && $this->belongsToCompany($authUser, $company);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Company');
    }

    public function update(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('Update:Company')
            && $this->belongsToCompany($authUser, $company);
    }

    public function delete(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('Delete:Company')
            && $this->belongsToCompany($authUser, $company);
    }

    public function restore(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('Restore:Company')
            && $this->belongsToCompany($authUser, $company);
    }

    public function forceDelete(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('ForceDelete:Company')
            && $this->belongsToCompany($authUser, $company);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Company');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Company');
    }

    public function replicate(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('Replicate:Company')
            && $this->belongsToCompany($authUser, $company);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Company');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Company');
    }
}