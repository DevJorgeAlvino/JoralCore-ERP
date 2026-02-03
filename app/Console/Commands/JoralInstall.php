<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class JoralInstall extends Command
{
    /**
     * El nombre que escribirás en la terminal.
     */
    protected $signature = 'joral:install {--force : Forzar la instalación sin preguntar}';

    /**
     * La descripción que sale al listar los comandos.
     */
    protected $description = 'Instala JoralCore desde cero (BD, Permisos, Seeders, Storage)';

    /**
     * Aquí ocurre la magia.
     */
    public function handle()
    {
        $this->info("🚀 Iniciando instalación de JoralCore System...");

        // 1. Advertencia de seguridad (Porque migrate:fresh borra todo)
        if (! $this->option('force') && ! $this->confirm('⚠️  ADVERTENCIA: Esto borrará TODA la base de datos. ¿Deseas continuar?')) {
            $this->warn('Cancelado por el usuario.');
            return;
        }

        // Barra de progreso para que se vea profesional
        $bar = $this->output->createProgressBar(5);
        $bar->start();

        // PASO 1: Generar Key (si no existe)
        if (! env('APP_KEY')) {
            $this->callSilent('key:generate');
        }
        $bar->advance();

        // PASO 2: Enlace simbólico de imágenes
        // Borramos el link anterior si existe para evitar conflictos
        if (File::exists(public_path('storage'))) {
            File::delete(public_path('storage'));
        }
        $this->callSilent('storage:link');
        $this->newLine();
        $this->info(' 📂 Storage linkeado.');
        $bar->advance();

        // PASO 3: Limpieza de Caché
        $this->callSilent('optimize:clear');
        $bar->advance();

        // PASO 4: Migración y Seeds (El paso pesado)
        $this->newLine();
        $this->info(' 🗄️  Creando tablas y sembrando datos (esto puede tardar)...');
        // Aquí llamamos a tu Seeder maestro que ya incluye a Shield
        $this->call('migrate:fresh');
        
        $bar->advance();

        // PASO 5: Regenerar Shield por si acaso (Opcional, doble seguridad)
        $this->info(' 🛡️  Asegurando permisos de Shield...');
        $this->callSilent('shield:generate', ['--all' => true]);
        $bar->advance();

        $this->info(' 👤 Sembrando datos de Administrador...');
        $this->call('db:seed');
        $bar->advance();
        
        $this->newLine(2);
        $this->info('✅ ¡JoralCore instalado correctamente!');
        $this->info('👤 Usuario: core@core.com');
        $this->info('🔑 Password: mimomimaximen30');
        $this->info('-------------------------------------------');
        $this->comment('Listo para que ingreses al sistema. Iniciando Proyecto...');

        $this->info(' � Iniciando Proyecto...');
        $this->call('composer run dev');
        $bar->advance();

        $bar->finish();
    }
}