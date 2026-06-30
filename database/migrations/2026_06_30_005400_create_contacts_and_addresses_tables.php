<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('type')->default('customer'); // 'customer', 'supplier', 'both'
            $table->string('name');
            $table->string('document_type'); // 'dni', 'ruc', 'run', 'rut', 'passport', 'foreign_id', 'other'
            $table->string('document_number');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('billing_payment_terms')->default('cash'); // 'cash', 'credit_30', 'credit_60', etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'type']);
            $table->unique(['company_id', 'document_type', 'document_number']);
        });

        Schema::create('contact_addresses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('label')->default('Oficina Principal'); // Ej: 'Oficina Central', 'Almacén Despacho'
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('state_region')->nullable(); // Región / Departamento / Estado
            $table->string('country', 2)->default('PE'); // Código de país ISO (PE, CL, etc.)
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_addresses');
        Schema::dropIfExists('contacts');
    }
};
