<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Validamos que exista usuario logueado
        if (! $user) {
            return redirect('/admin/login');
        }

        // 2. Reseteamos el team_id de Spatie a NULL para el panel Admin (central).
        //    Esto evita que un team_id residual de una sesión previa en el
        //    panel Company filtre incorrectamente los roles del super_admin.
        setPermissionsTeamId(null);

        // 3. Verificamos si tiene el rol 'super_admin' IGNORANDO la empresa actual
        //    Esto detecta tu rol global (company_id = NULL)
        $isSuperAdmin = $user->roles()
            ->withoutGlobalScopes()
            ->where('name', 'super_admin')
            ->exists();

        if ($isSuperAdmin) {

            return $next($request);
        }

        Notification::make()
            ->title('Acceso Restringido')
            ->body('No tienes permisos de Administrador Global. Te hemos redirigido a tu panel.')
            ->warning()
            ->duration(5000)
            ->send();

        return redirect('/company');
    }
}
