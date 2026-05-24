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
        Schema::table('companies', function (Blueprint $table) {
            // ──────────────────────────────────────────────
            // Información legal y tributaria
            // ──────────────────────────────────────────────
            $table->string('legal_name')
                ->after('name')
                ->nullable()
                ->comment('Razón social registrada ante la autoridad tributaria');

            $table->string('identity_document', 20)
                ->after('legal_name')
                ->nullable()
                ->comment('RUC (PE, 11 dígitos) o RUT (CL, 8-9 dígitos sin guión ni DV)');

            $table->char('dv', 1)
                ->after('identity_document')
                ->nullable()
                ->comment('Dígito verificador del RUT chileno (0-9 o K)');

            $table->string('economic_activity_code', 10)
                ->after('dv')
                ->nullable()
                ->comment('Código CIIU (PE) o código de actividad económica SII (CL)');

            // ──────────────────────────────────────────────
            // Localización y moneda
            // ──────────────────────────────────────────────
            $table->char('country', 2)
                ->after('description')
                ->default('PE')
                ->comment('Código ISO 3166-1 alpha-2: PE o CL');

            $table->char('currency', 3)
                ->after('country')
                ->default('PEN')
                ->comment('Moneda base ISO 4217: PEN o CLP');

            $table->string('timezone', 50)
                ->after('currency')
                ->default('America/Lima')
                ->comment('Huso horario IANA de la empresa');

            // ──────────────────────────────────────────────
            // Dirección fiscal
            // ──────────────────────────────────────────────
            $table->string('tax_address')
                ->after('timezone')
                ->nullable()
                ->comment('Dirección fiscal completa (domicilio legal)');

            $table->string('geo_code', 10)
                ->after('tax_address')
                ->nullable()
                ->comment('Ubigeo INEI (PE, 6 dígitos) o código de comuna (CL, 5 dígitos)');

            // ──────────────────────────────────────────────
            // Contacto corporativo
            // ──────────────────────────────────────────────
            $table->string('phone', 20)
                ->after('geo_code')
                ->nullable()
                ->comment('Teléfono corporativo con código de país');

            $table->string('email')
                ->after('phone')
                ->nullable()
                ->comment('Correo electrónico corporativo');

            // ──────────────────────────────────────────────
            // Índices
            // ──────────────────────────────────────────────
            $table->index('country');
            $table->index('identity_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['country']);
            $table->dropIndex(['identity_document']);

            $table->dropColumn([
                'legal_name',
                'identity_document',
                'dv',
                'economic_activity_code',
                'country',
                'currency',
                'timezone',
                'tax_address',
                'geo_code',
                'phone',
                'email',
            ]);
        });
    }
};
