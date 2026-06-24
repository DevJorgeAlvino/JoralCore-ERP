<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. CATEGORIES ────────────────────────────────────────────────────
        Schema::create('categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();     // heroicon name
            $table->string('color', 9)->nullable(); // hex color, e.g. #3B82F6
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'slug']);
        });

        // ── 2. BRANDS ────────────────────────────────────────────────────────
        Schema::create('brands', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'slug']);
        });

        // ── 3. ALTER ITEMS → add category_id and brand_id ────────────────────
        Schema::table('items', function (Blueprint $table) {
            $table->foreignUlid('category_id')->nullable()->after('type')
                ->constrained('categories')->nullOnDelete();
            $table->foreignUlid('brand_id')->nullable()->after('category_id')
                ->constrained('brands')->nullOnDelete();
        });

        // ── 4. ITEM PRESENTATIONS ─────────────────────────────────────────────
        Schema::create('item_presentations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignUlid('unit_measure_id')->nullable()->constrained('unit_measures')->nullOnDelete();
            $table->string('name');                           // Ej: "Botella 1L", "Caja x24"
            $table->string('barcode')->nullable()->index();   // Código de barras de esta presentación
            $table->decimal('conversion_factor', 12, 4)->default(1.0000); // Factor vs presentación base
            $table->boolean('is_default')->default(false);    // Presentación principal del ítem
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── 5. ITEM PRICES ────────────────────────────────────────────────────
        Schema::create('item_prices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('item_presentation_id')->constrained('item_presentations')->cascadeOnDelete();
            $table->string('price_list_name')->default('General'); // Ej: "Minorista", "Mayorista"
            $table->decimal('purchase_cost', 12, 4)->default(0.0000);
            $table->decimal('sale_price', 12, 4)->default(0.0000);
            $table->string('currency', 3)->default('PEN');    // PEN, CLP, USD
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── 6. ITEM STOCKS ────────────────────────────────────────────────────
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('item_presentation_id')->unique()->constrained('item_presentations')->cascadeOnDelete();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->decimal('current_stock', 12, 4)->default(0.0000);
            $table->decimal('minimum_stock', 12, 4)->default(0.0000);
            $table->decimal('maximum_stock', 12, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_stocks');
        Schema::dropIfExists('item_prices');
        Schema::dropIfExists('item_presentations');

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'brand_id']);
        });

        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
