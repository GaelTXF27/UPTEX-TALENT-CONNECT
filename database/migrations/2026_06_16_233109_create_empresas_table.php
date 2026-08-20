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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre de la empresa (Obligatorio)
            $table->string('sector')->nullable(); // Ej: Tecnología, Manufactura
            $table->string('ubicacion')->nullable(); // Ej: Estado de México, CDMX
            $table->text('direccion')->nullable(); // Dirección física detallada
            $table->text('descripcion')->nullable(); // Acerca de la empresa
            $table->string('telefono')->nullable(); // Teléfono de contacto
            $table->string('email')->nullable(); // Correo electrónico de contacto
            $table->string('web')->nullable(); // Sitio web oficial
            $table->timestamps(); // Crea automáticamente created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};