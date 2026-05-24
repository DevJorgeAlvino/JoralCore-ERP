<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Limpiar datos existentes para evitar errores de llave nula o duplicados
        DB::table('items')->delete();
        DB::table('unit_measures')->delete();

        // 1. Eliminar la relación vieja
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['unit_code']);
            $table->dropColumn('unit_code');
        });

        // 2. Modificar la tabla unit_measures
        Schema::table('unit_measures', function (Blueprint $table) {
            $table->dropPrimary(); // Quitar la llave primaria 'code'
        });

        Schema::table('unit_measures', function (Blueprint $table) {
            $table->ulid('id')->first();
            $table->foreignUuid('company_id')->after('id')->constrained('companies')->cascadeOnDelete();
            
            // Reasignar la llave primaria al nuevo ID
            $table->primary('id');
            // Hacer que el código sea único por empresa
            $table->unique(['company_id', 'code']);
        });

        // 3. Agregar la nueva relación en items
        Schema::table('items', function (Blueprint $table) {
            $table->foreignUlid('unit_measure_id')->nullable()->after('type')->constrained('unit_measures')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['unit_measure_id']);
            $table->dropColumn('unit_measure_id');
            $table->string('unit_code', 5)->nullable();
        });

        Schema::table('unit_measures', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'code']);
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->dropPrimary();
            $table->dropColumn('id');
            $table->primary('code');
        });
    }
};
