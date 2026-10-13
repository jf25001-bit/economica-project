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
            $table->boolean('anulado')->default(false);
        });

        DB::table('productos')->whereNotNull('deleted_at')->update(['anulado' => true]);

        Schema::table('productos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->softDeletes();
        });

        DB::table('productos')->where('anulado', true)->update(['deleted_at' => now()]);

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('anulado');
        });
    }
};