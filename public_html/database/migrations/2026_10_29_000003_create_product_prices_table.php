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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id()->comment('Identificador único del registro de precio');
            $table->foreignId('product_id')
                ->comment('Identificador del producto')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('currency_id')
                ->comment('Identificador de la divisa')
                ->constrained('currencies')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->decimal('price', 15, 2)->comment('Precio del producto en la divisa especificada');
            $table->timestamps();

            $table->unique(['product_id', 'currency_id'], 'uk_product_currency_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
