<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacante_id')->constrained('vacantes')->onDelete('cascade');
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('nombre');
            $table->string('correo');
            $table->string('telefono')->nullable();
            $table->string('carrera')->nullable();
            $table->text('mensaje')->nullable();
            $table->string('cv_path');
            $table->string('estado')->default('Pendiente');
            $table->timestamps();

            $table->unique(['vacante_id', 'correo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};
