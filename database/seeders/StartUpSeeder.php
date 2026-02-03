<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company; // Asegúrate que este sea tu modelo de Empresa
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class StartUpSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GENERAR PERMISOS Y ROL SUPER_ADMIN (Magia de Shield) 🛡️
        // Esto escanea tus policies y crea los permisos en la BD + el rol 'super_admin'
        $this->command->info('Generando permisos y roles de Shield...');
        Artisan::call('shield:generate --all --ignore-existing-policies');
        
        // Limpiamos caché de permisos para evitar errores
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        // 2. CREAR LA EMPRESA PRINCIPAL 🏢
        // Necesitamos una empresa para que el usuario pueda entrar al panel Company
        // $company = Company::firstOrCreate(
        //     ['name' => 'JoralCore Corp'], // Condición de búsqueda
        //     [
        //         'name' => 'JoralCore Corp',
        //         // Si tienes campos obligatorios en companies, ponlos aquí
        //         'settings' => ['currency' => 'PEN', 'tax_rate' => 18], 
        //     ]
        // );
        // $this->command->info('Empresa creada: ' . $company->name);


        // 3. CREAR EL USUARIO ADMINISTRADOR 👤
       $user = User::first() ?? User::create([
            'name' => 'Core',
            'email' => 'core@core.dev',
            'password' => bcrypt('mimomimaximen30'),
            'email_verified_at' => now(),
        ]);
        
        // Vinculamos el usuario a la empresa (Relación Tenant)
        // IMPORTANTE: Ajusta esto según cómo guardas la relación (tabla pivote o columna)
        // Opción A: Si es BelongsTo (columna company_id en users)
        // $user->company_id = $company->id;
        // $user->save();
        
        // Opción B: Si es BelongsToMany (tabla pivote company_user) - LO MÁS COMÚN
        // if (!$user->companies()->where('company_id', $company->id)->exists()) {
        //      $user->companies()->attach($company);
        //      // Forzamos que sea su empresa actual (si usas columna current_company_id)
        //      // $user->current_company_id = $company->id; 
        //      // $user->save();
        // }

        // $this->command->info('Usuario Super Admin creado: core@core.com');


        // 4. ASIGNAR EL ROL SUPER ADMIN (El paso crucial) 👑
        
        // A. Rol Global (Super Admin suele ser global, team_id = NULL)
        // Esto le permite entrar al Panel Admin y saltarse todo con el Gate::before
        setPermissionsTeamId(null); // Aseguramos contexto global
        
        if (! $user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
            $this->command->info('Rol Super Admin asignado correctamente.');
        }

        // B. (Opcional) Rol dentro de la Empresa
        // Si quieres que TAMBIÉN tenga un rol específico dentro del panel Company
        // setPermissionsTeamId($company->id);
        // $roleGerente = Role::firstOrCreate(['name' => 'Gerente', 'guard_name' => 'web']);
        // $user->assignRole($roleGerente);
    }
}