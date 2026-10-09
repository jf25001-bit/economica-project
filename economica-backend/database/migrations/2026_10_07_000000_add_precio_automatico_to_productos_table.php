<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tieneCosto = Schema::hasColumn('productos', 'precio_compra');

        Schema::table('productos', function (Blueprint $table) use ($tieneCosto) {
            if (!Schema::hasColumn('productos', 'margen_porcentaje')) {
                $table->decimal('margen_porcentaje', 6, 2)->nullable();
            }

            if (!Schema::hasColumn('productos', 'precio_automatico')) {
                $table->boolean('precio_automatico')->default(false);
            }

            // Costo unitario con 4 decimales para no perder precisión en el promedio
            if ($tieneCosto) {
                $table->decimal('precio_compra', 12, 4)->nullable()->default(0)->change();
            } else {
                $table->decimal('precio_compra', 12, 4)->nullable()->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'margen_porcentaje')) {
                $table->dropColumn('margen_porcentaje');
            }

            if (Schema::hasColumn('productos', 'precio_automatico')) {
                $table->dropColumn('precio_automatico');
            }
        });
    }
};