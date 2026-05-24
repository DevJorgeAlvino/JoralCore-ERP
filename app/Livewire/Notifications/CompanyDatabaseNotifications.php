<?php

namespace App\Livewire\Notifications;

use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Facades\Filament;

class CompanyDatabaseNotifications extends DatabaseNotifications
{
    /**
     * Filtra las notificaciones solo por la empresa activa (Tenant).
     * Las notificaciones del Admin (company_id = null) no aparecen aquí.
     */
    public function getNotificationsQuery(): Builder | Relation
    {
        $user = $this->getUser();

        if (! $user) {
            abort(401);
        }

        $companyId = Filament::getTenant()?->id;

        return $user->notifications()
            ->where('data->format', 'filament')
            ->where(function ($query) use ($companyId) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.company_id')) = ?", [$companyId]);
            });
    }
}
