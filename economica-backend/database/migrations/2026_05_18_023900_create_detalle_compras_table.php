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
        Schema::create('detalle_compras', function (Blueprint $table) {
    $table->id();
     // Compra a la que pertenece
    $table->foreignId('compra_id')
          ->constrained('compras')
->cascadeOnDelete();
// Producto comprado
    $table->foreignId('producto_id')
      ->nullable()
          ->constrained('productos')

->nullOnDelete();

            // Registro histórico fijo
            $table->integer('cantidad')
                ->nullable();

            $table->decimal('precio_compra', 10, 2)
                ->nullable();

            $table->decimal('subtotal', 10, 2)
                ->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
