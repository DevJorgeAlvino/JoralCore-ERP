<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserCompanyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user= auth()->user();

        if(!$user){
            return redirect()->route('filament.company.auth.login');
        }

        $tenant = Filament::getTenant();

        if ($tenant) {

            setPermissionsTeamId($tenant->id);

            return $next($request);
        }

        $company = $user->companies()->first();

        if(!$company){

            return redirect()->route('filament.company.auth.login');
        }

        Filament::setTenant($company);
        
        return $next($request);
    }
}
