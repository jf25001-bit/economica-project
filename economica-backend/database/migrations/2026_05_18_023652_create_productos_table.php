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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barras', 50)->unique()->nullable();
            $table->string('nombre', 100)->unique();
            $table->decimal('precio_compra', 10, 2)->default(0.00);
            $table->decimal('precio_venta', 10, 2)->default(0.00);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('imagen')->nullable();
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('cascade');

            // RELACIONES (Llaves Foráneas)
            // 1. Relación con sub_categorias
            $table->foreignId('sub_categoria_id')
                  ->nullable()
                  ->constrained('sub_categorias')
                  ->nullOnDelete();

            // 2. Relación con proveedors (recuerda que tu tabla se llama proveedors)
            $table->foreignId('proveedor_id')
                  ->constrained('proveedors')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
