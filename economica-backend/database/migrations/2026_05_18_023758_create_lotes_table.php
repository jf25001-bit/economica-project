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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();

            // De qué detalle de compra nació
            $table->foreignId('detalle_compra_id')
                ->nullable()
                ->constrained('detalle_compras')
                ->nullOnDelete();

            // Producto del lote
            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete();

            // Código o número de lote
            $table->string('codigo_lote')
                ->nullable();

            // Cantidad original comprada
            $table->integer('cantidad_inicial')
                ->nullable();

            // Cantidad restante (esta baja con ventas)
            $table->integer('cantidad_actual')
                ->nullable();

            // Fecha de vencimiento del lote
            $table->date('fecha_expiracion')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};