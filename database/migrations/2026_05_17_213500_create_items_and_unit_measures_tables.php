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
        Schema::create('unit_measures', function (Blueprint $table) {
            $table->string('code', 5)->primary();
            $table->string('name');
            $table->string('country', 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('sku')->index();
            $table->string('barcode')->nullable()->index();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('type')->default('product'); // 'product', 'service'
            $table->string('unit_code', 5);
            $table->foreign('unit_code')->references('code')->on('unit_measures');
            $table->decimal('purchase_cost', 12, 4)->default(0.0000);
            $table->decimal('sale_price', 12, 4)->default(0.0000);
            $table->string('tax_type')->default('gravado'); // 'gravado', 'exonerado', 'inafecto', 'exento'
            $table->json('specific_taxes')->nullable();
            $table->boolean('manage_stock')->default(true);
            $table->decimal('current_stock', 12, 2)->default(0.00);
            $table->decimal('minimum_stock', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('unit_measures');
    }
};
