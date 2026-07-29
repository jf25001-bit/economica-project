<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barras', 50)->unique()->nullable();
            $table->string('nombre', 100)->unique();
            $table->decimal('precio_venta', 10, 2)->default(0.00);
            $table->string('unidad_medida', 20)->default('pza');
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);

            $table->foreignId('sub_categoria_id')
                  ->constrained('sub_categorias')
                  ->onDelete('cascade');

            $table->foreignId('proveedor_id')
                  ->constrained('proveedors')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};