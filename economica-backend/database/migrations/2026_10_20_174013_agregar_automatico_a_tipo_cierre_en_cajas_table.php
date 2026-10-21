<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->enum('tipo_cierre', ['normal', 'forzado', 'automatico'])->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('cajas')->where('tipo_cierre', 'automatico')->update(['tipo_cierre' => 'normal']);

        Schema::table('cajas', function (Blueprint $table) {
            $table->enum('tipo_cierre', ['normal', 'forzado'])->nullable()->change();
        });
    }
};