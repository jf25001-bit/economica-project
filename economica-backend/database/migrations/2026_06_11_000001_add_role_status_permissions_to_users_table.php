<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rol_id')) {
                $table->foreignId('rol_id')
                    ->nullable()
                    ->after('password')
                    ->constrained('rols')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'activo')) {
                $table->boolean('activo')->default(true)->after('rol_id');
            }

            if (!Schema::hasColumn('users', 'permisos')) {
                $table->json('permisos')->nullable()->after('activo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'rol_id')) {
                $table->dropConstrainedForeignId('rol_id');
            }

            if (Schema::hasColumn('users', 'activo')) {
                $table->dropColumn('activo');
            }

            if (Schema::hasColumn('users', 'permisos')) {
                $table->dropColumn('permisos');
            }
        });
    }
};
