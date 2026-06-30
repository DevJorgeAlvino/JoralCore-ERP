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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('legal_type')->default('person')->after('type'); // 'person', 'company'
            $table->string('business_activity')->nullable()->after('document_number'); // Giro Comercial (Obligatorio en Chile)
            $table->decimal('credit_limit', 12, 4)->default(0.0000)->after('billing_payment_terms');
        });

        Schema::table('contact_addresses', function (Blueprint $table) {
            $table->string('type')->default('shipping')->after('contact_id'); // 'fiscal', 'shipping'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_addresses', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['legal_type', 'business_activity', 'credit_limit']);
        });
    }
};
