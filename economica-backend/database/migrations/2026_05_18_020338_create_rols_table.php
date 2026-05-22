<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambiamos 'rols' por 'roles'
        Schema::create('rols', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique(); // Administrador, Encargado
            $table->string('descripcion', 255)->nullable(); // Una breve reseña de qué hace
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rols');
    }
};
