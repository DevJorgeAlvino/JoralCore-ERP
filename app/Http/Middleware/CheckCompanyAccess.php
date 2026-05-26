<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si el usuario no tiene ninguna empresa asignada...
        if ($user && $user->companies()->count() === 0) {
            
            // Verificamos si es super admin
            $isSuperAdmin = $user->roles()
                ->withoutGlobalScopes()
                ->where('name', 'super_admin')
                ->exists();

            if ($isSuperAdmin) {
                \Filament\Notifications\Notification::make()
                    ->title('Acceso Restringido')
                    ->body('No tienes ninguna empresa asignada. Te hemos redirigido al panel de Administración Global.')
                    ->warning()
                    ->duration(5000)
                    ->send();

                return redirect('/admin');
            }

            // Si no es super admin ni tiene empresas, probablemente su cuenta fue suspendida o está mal configurada
            \Filament\Notifications\Notification::make()
                ->title('Sin Acceso')
                ->body('Tu usuario no tiene ninguna empresa asignada actualmente. Contacta al administrador.')
                ->danger()
                ->duration(5000)
                ->send();

            // Desloguearlo o mandarlo al home, por seguridad
            auth()->logout();
            return redirect('/');
        }

        return $next($request);
    }
}
