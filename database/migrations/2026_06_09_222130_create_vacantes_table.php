<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacantes', function (Blueprint $table) {
            $table->id();
            $table->string('puesto');
            $table->string('empresa');
            $table->string('tipo_jornada'); // Tiempo Completo, Medio Tiempo, Remoto, etc.
            $table->string('ubicacion')->nullable();
            $table->text('descripcion');
            $table->boolean('activa')->default(true); // Para poder pausar vacantes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacantes');
    }
};