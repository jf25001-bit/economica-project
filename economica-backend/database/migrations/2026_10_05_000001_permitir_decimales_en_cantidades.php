<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_compras', function (Blueprint $table) {
            $table->decimal('cantidad', 10, 2)->change();
        });

        Schema::table('lotes', function (Blueprint $table) {
            $table->decimal('cantidad_inicial', 10, 2)->change();
            $table->decimal('cantidad_actual', 10, 2)->default(0)->change();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('stock', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('detalle_compras', function (Blueprint $table) {
            $table->integer('cantidad')->change();
        });

        Schema::table('lotes', function (Blueprint $table) {
            $table->integer('cantidad_inicial')->change();
            $table->integer('cantidad_actual')->default(0)->change();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock')->default(0)->change();
        });
    }
};