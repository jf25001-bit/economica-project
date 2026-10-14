<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->string('producto_nombre', 100)->nullable()->after('producto_id');
            $table->string('producto_codigo_barras', 50)->nullable()->after('producto_nombre');
        });

        Schema::table('detalle_compras', function (Blueprint $table) {
            $table->string('producto_nombre', 100)->nullable()->after('producto_id');
            $table->string('producto_codigo_barras', 50)->nullable()->after('producto_nombre');
            $table->string('proveedor_nombre', 150)->nullable()->after('proveedor_id');
        });

        Schema::table('lotes', function (Blueprint $table) {
            $table->string('producto_nombre', 100)->nullable()->after('producto_id');
            $table->string('producto_codigo_barras', 50)->nullable()->after('producto_nombre');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->string('vendedor_nombre', 150)->nullable()->after('user_id');
        });

        foreach (['detalle_ventas', 'detalle_compras', 'lotes'] as $tabla) {
            DB::table($tabla)
                ->join('productos', 'productos.id', '=', "$tabla.producto_id")
                ->update([
                    "$tabla.producto_nombre" => DB::raw('productos.nombre'),
                    "$tabla.producto_codigo_barras" => DB::raw('productos.codigo_barras'),
                ]);
        }

        $columnaProveedor = Schema::hasColumn('proveedores', 'nombre_proveedor') ? 'nombre_proveedor' : 'nombre';

        DB::table('detalle_compras')
            ->join('proveedores', 'proveedores.id', '=', 'detalle_compras.proveedor_id')
            ->update(['detalle_compras.proveedor_nombre' => DB::raw("proveedores.$columnaProveedor")]);

        $columnaUsuario = collect(['nombre', 'name', 'username'])
            ->first(fn ($c) => Schema::hasColumn('users', $c));

        if ($columnaUsuario) {
            DB::table('ventas')
                ->join('users', 'users.id', '=', 'ventas.user_id')
                ->update(['ventas.vendedor_nombre' => DB::raw("users.$columnaUsuario")]);
        }
    }

    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn(['producto_nombre', 'producto_codigo_barras']);
        });

        Schema::table('detalle_compras', function (Blueprint $table) {
            $table->dropColumn(['producto_nombre', 'producto_codigo_barras', 'proveedor_nombre']);
        });

        Schema::table('lotes', function (Blueprint $table) {
            $table->dropColumn(['producto_nombre', 'producto_codigo_barras']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('vendedor_nombre');
        });
    }
};