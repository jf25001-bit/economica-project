<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'categoria_id')) {
                $table->foreignId('categoria_id')
                    ->nullable()
                    ->after('stock_minimo')
                    ->constrained('categorias')
                    ->nullOnDelete();
            }
        });

        try {
            DB::statement('ALTER TABLE productos DROP FOREIGN KEY productos_sub_categoria_id_foreign');
        } catch (Throwable $error) {
            //
        }

        DB::statement('ALTER TABLE productos MODIFY sub_categoria_id BIGINT UNSIGNED NULL');

        try {
            DB::statement('ALTER TABLE productos ADD CONSTRAINT productos_sub_categoria_id_foreign FOREIGN KEY (sub_categoria_id) REFERENCES sub_categorias(id) ON DELETE SET NULL');
        } catch (Throwable $error) {
            //
        }

        DB::statement('
            UPDATE productos p
            INNER JOIN sub_categorias s ON s.id = p.sub_categoria_id
            SET p.categoria_id = s.categoria_id
            WHERE p.categoria_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'categoria_id')) {
                $table->dropConstrainedForeignId('categoria_id');
            }
        });
    }
};
