<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            if (!Schema::hasColumn('lotes', 'detalle_compra_id')) {
                $table->foreignId('detalle_compra_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('detalle_compras')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('lotes', 'producto_id')) {
                $table->foreignId('producto_id')
                    ->nullable()
                    ->after('detalle_compra_id')
                    ->constrained('productos')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('lotes', 'codigo_lote')) {
                $table->string('codigo_lote')->nullable()->after('producto_id');
            }

            if (!Schema::hasColumn('lotes', 'cantidad_inicial')) {
                $table->integer('cantidad_inicial')->nullable()->after('codigo_lote');
            }

            if (!Schema::hasColumn('lotes', 'cantidad_actual')) {
                $table->integer('cantidad_actual')->nullable()->after('cantidad_inicial');
            }

            if (!Schema::hasColumn('lotes', 'fecha_expiracion')) {
                $table->date('fecha_expiracion')->nullable()->after('cantidad_actual');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            if (Schema::hasColumn('lotes', 'detalle_compra_id')) {
                $table->dropConstrainedForeignId('detalle_compra_id');
            }
        });
    }
};
