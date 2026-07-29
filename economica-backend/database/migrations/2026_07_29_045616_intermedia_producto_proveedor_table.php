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
        if (Schema::hasColumn('productos', 'proveedor_id')) {
            Schema::table('productos', function (Blueprint $table) {
                // Notar que la FK original apuntaba a 'proveedors' según tu migración
                $table->dropForeign(['proveedor_id']);
                $table->dropColumn('proveedor_id');
            });
        }

        // 2. Creamos la tabla pivote producto_proveedor
        Schema::create('producto_proveedor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade');

            $table->foreignId('proveedor_id')
                  ->constrained('proveedores') // Apunta a la tabla 'proveedores'
                  ->onDelete('cascade');

            $table->timestamps();

            // Evitamos duplicar la relación entre el mismo producto y proveedor
            $table->unique(['producto_id', 'proveedor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_proveedor');

        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('proveedor_id')
                  ->nullable()
                  ->constrained('proveedors')
                  ->onDelete('cascade');
        });
    }
    
};
