<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitMeasurePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UnitMeasure');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:UnitMeasure');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UnitMeasure');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:UnitMeasure');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:UnitMeasure');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:UnitMeasure');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:UnitMeasure');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UnitMeasure');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UnitMeasure');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:UnitMeasure');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UnitMeasure');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UnitMeasure');
    }

}
