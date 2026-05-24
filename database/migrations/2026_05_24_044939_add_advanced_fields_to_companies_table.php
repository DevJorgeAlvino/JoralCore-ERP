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
            $table->boolean('is_active')->default(true)->after('description');
            $table->string('website')->nullable()->after('email');
            $table->string('tax_regime')->nullable()->after('economic_activity_code')->comment('Régimen tributario (MYPE, General, etc.)');
            $table->boolean('is_retention_agent')->default(false)->after('tax_regime');
            $table->string('legal_rep_name')->nullable()->after('tax_address');
            $table->string('legal_rep_document')->nullable()->after('legal_rep_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'website',
                'tax_regime',
                'is_retention_agent',
                'legal_rep_name',
                'legal_rep_document',
            ]);
        });
    }
};
