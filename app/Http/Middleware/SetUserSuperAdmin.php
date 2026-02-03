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
            return redirect('/admin/login'); // O la ruta de login que uses
        }

        // 2. Verificamos si tiene el rol 'super_admin' IGNORANDO la empresa actual
        // Esto detecta tu rol global (company_id = NULL)
        $isSuperAdmin = $user->roles()
            ->withoutGlobalScopes() 
            ->where('name', 'super_admin')
            ->exists();

        if ($isSuperAdmin) {
            return $next($request);
        }

        Notification::make()
            ->title('Acceso Restringido') // Título
            ->body('No tienes permisos de Administrador Global. Te hemos redirigido a tu panel.') // Mensaje
            ->warning() // Color amarillo (o usa ->danger() para rojo)
            ->duration(5000) // Cuánto tiempo dura (5 seg)
            ->send(); // <--- EL TRUCO: Esto guard

        return redirect('/company');

        // 3. Si no es admin, lo sacamos (Error 403 o Redirect)
        // abort(403, 'ACCESO DENEGADO: Solo para Super Administradores.');

    }
}
