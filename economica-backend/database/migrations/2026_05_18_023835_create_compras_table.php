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
       Schema::create('compras', function (Blueprint $table) {
    $table->id();
    $table->decimal('total', 10, 2)->default(0.00);

    // Relación explícita apuntando a la tabla real 'proveedors'
    $table->unsignedBigInteger('proveedor_id');
    $table->foreign('proveedor_id')
          ->references('id')
          ->on('proveedors')
          ->onDelete('cascade');

    // Relación con el usuario que registra la compra
    $table->foreignId('user_id')
          ->constrained('users')
          ->onDelete('cascade');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
