<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiamos la caché de permisos para evitar errores "fantasma"
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. "Apagamos" el sistema de Teams momentáneamente
        // Esto es vital para que company_id se guarde como NULL
        setPermissionsTeamId(null);

        // 3. Creamos (o buscamos) el Usuario Super Admin
        $user = User::firstOrCreate(
            ['email' => 'core@core.dev'], // Cambia esto por tu email
            [
                'name' => 'Core',
                'password' => Hash::make('password'), // Tu contraseña segura
                'email_verified_at' => now()
                // Si tienes campos obligatorios extra, ponlos aquí
            ]
        );

        // 4. Creamos el Rol Global (company_id = NULL)
        // Usamos firstOrCreate para que no falle si Shield ya lo creó
        $role = Role::firstOrCreate(
            [
                'name' => 'super_admin', 
                'guard_name' => 'web',
                'company_id' => null // <--- ESTO ES LO IMPORTANTE
            ]
        );

        // 5. Asignamos el rol
        // Gracias al paso 2, esto insertará NULL en la tabla pivote automáticamente
        if (! $user->hasRole('super_admin')) {
            $user->assignRole($role);
        }

        $this->command->info("¡Super Admin Global creado exitosamente! 🚀");
        $this->command->info("Usuario: {$user->email}");
        $this->command->info("Rol: {$role->name} (Company ID: NULL)");
    }
}