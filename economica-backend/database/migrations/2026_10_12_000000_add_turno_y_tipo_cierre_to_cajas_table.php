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
            $table->enum('turno', ['unico', 'manana', 'tarde'])->default('unico')->after('estado');
            $table->enum('tipo_cierre', ['normal', 'forzado'])->nullable()->after('turno');
        });

        // Datos viejos: los que tienen cerrado_por_id ya eran forzados
        DB::table('cajas')->where('estado', 'cerrada')->whereNotNull('cerrado_por_id')->update(['tipo_cierre' => 'forzado']);
        DB::table('cajas')->where('estado', 'cerrada')->whereNull('cerrado_por_id')->update(['tipo_cierre' => 'normal']);
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['turno', 'tipo_cierre']);
        });
    }
};