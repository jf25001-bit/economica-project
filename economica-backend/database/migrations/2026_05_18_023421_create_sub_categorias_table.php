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
        Schema::create('sub_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50); // Ej: Gaseosas, Aguas
            // RELACIÓN: Creamos la clave foránea que apunta a la tabla 'categorias'
            $table->foreignId('categoria_id')
                  ->constrained('categorias') // Indica que se conecta con la tabla 'categorias'
                  ->onDelete('cascade');     // Si se borra la categoría madre, se borran sus subcategorías

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categorias');
    }
};
