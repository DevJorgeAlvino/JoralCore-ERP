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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);
        });

        // Recreamos item_stocks con scope por almacén en vez de empresa global
        Schema::dropIfExists('item_stocks');
        
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignUlid('item_presentation_id')->constrained('item_presentations')->cascadeOnDelete();
            $table->decimal('current_stock', 12, 4)->default(0.0000);
            $table->decimal('minimum_stock', 12, 4)->default(0.0000);
            $table->decimal('maximum_stock', 12, 4)->nullable();
            $table->timestamps();

            $table->unique(['warehouse_id', 'item_presentation_id']);
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignUlid('item_presentation_id')->constrained('item_presentations')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // 'in', 'out', 'transfer'
            $table->string('concept'); // Ej: 'Venta', 'Compra', 'Merma', 'Ajuste', 'Inventario Inicial'
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_cost', 12, 4)->default(0.0000);
            $table->decimal('balance_stock', 12, 4)->default(0.0000);
            $table->string('reference_document')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'warehouse_id', 'item_presentation_id'], 'idx_kardex_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');

        Schema::dropIfExists('item_stocks');

        // Recreamos la estructura original de item_stocks
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('item_presentation_id')->unique()->constrained('item_presentations')->cascadeOnDelete();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->decimal('current_stock', 12, 4)->default(0.0000);
            $table->decimal('minimum_stock', 12, 4)->default(0.0000);
            $table->decimal('maximum_stock', 12, 4)->nullable();
            $table->timestamps();
        });

        Schema::dropIfExists('warehouses');
    }
};
