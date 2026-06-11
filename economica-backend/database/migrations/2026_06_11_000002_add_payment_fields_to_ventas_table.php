<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'efectivo_recibido')) {
                $table->decimal('efectivo_recibido', 10, 2)->default(0)->after('total');
            }

            if (!Schema::hasColumn('ventas', 'cambio')) {
                $table->decimal('cambio', 10, 2)->default(0)->after('efectivo_recibido');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'cambio')) {
                $table->dropColumn('cambio');
            }

            if (Schema::hasColumn('ventas', 'efectivo_recibido')) {
                $table->dropColumn('efectivo_recibido');
            }
        });
    }
};
