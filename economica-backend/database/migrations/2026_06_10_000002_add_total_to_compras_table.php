<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('compras', 'total')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->decimal('total', 10, 2)->default(0.00)->after('fecha_llegada');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('compras', 'total')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->dropColumn('total');
            });
        }
    }
};
