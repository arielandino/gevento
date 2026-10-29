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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la divisa');
            $table->string('name', 100)->unique()->comment('Nombre o código único de la divisa');
            $table->string('symbol', 10)->comment('Símbolo de la divisa');
            $table->decimal('exchange_rate', 15, 4)->default(1.0000)->comment('Tasa de cambio de la divisa respecto a la base');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
