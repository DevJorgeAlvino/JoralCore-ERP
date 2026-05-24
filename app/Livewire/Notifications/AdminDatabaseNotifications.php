<?php

namespace App\Livewire\Notifications;

use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class AdminDatabaseNotifications extends DatabaseNotifications
{
    /**
     * Filtra las notificaciones del panel Admin.
     * Solo muestra notificaciones donde company_id es null (nivel global).
     */
    public function getNotificationsQuery(): Builder | Relation
    {
        $user = $this->getUser();

        if (! $user) {
            abort(401);
        }

        return $user->notifications()
            ->where('data->format', 'filament')
            ->where(function ($query) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.company_id')) IS NULL")
                      ->orWhereRaw("JSON_EXTRACT(data, '$.company_id') IS NULL");
            });
    }
}
