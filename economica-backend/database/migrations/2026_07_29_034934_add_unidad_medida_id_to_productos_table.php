<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
        
        $table->dropColumn('unidad_medida');

        
        $table->foreignId('unidad_medida_id')
              ->nullable()
              ->after('proveedor_id')
              ->constrained('unidad_medidas')
              ->nullOnDelete();
             });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            
            $table->dropForeign(['unidad_medida_id']);
            $table->dropColumn('unidad_medida_id');
            $table->string('unidad_medida', 20)->default('pza');
        });
    }
};