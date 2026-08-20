<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('rol');
            }

            if (!Schema::hasColumn('usuarios', 'estado_laboral')) {
                $table->string('estado_laboral')->nullable()->after('empresa_id');
            }

            if (!Schema::hasColumn('usuarios', 'carrera')) {
                $table->string('carrera')->nullable()->after('estado_laboral');
            }

            if (!Schema::hasColumn('usuarios', 'estatus')) {
                $table->string('estatus')->default('activo')->after('carrera');
            }
        });

        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('empresas', 'estado')) {
                $table->string('estado')->default('activa')->after('web');
            }
        });

        Schema::table('vacantes', function (Blueprint $table) {
            if (!Schema::hasColumn('vacantes', 'sector')) {
                $table->string('sector')->nullable()->after('empresa');
            }

            if (!Schema::hasColumn('vacantes', 'salario_ofrecido')) {
                $table->decimal('salario_ofrecido', 10, 2)->nullable()->after('ubicacion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vacantes', function (Blueprint $table) {
            $columnas = array_filter([
                Schema::hasColumn('vacantes', 'sector') ? 'sector' : null,
                Schema::hasColumn('vacantes', 'salario_ofrecido') ? 'salario_ofrecido' : null,
            ]);

            if ($columnas !== []) {
                $table->dropColumn($columnas);
            }
        });

        Schema::table('empresas', function (Blueprint $table) {
            if (Schema::hasColumn('empresas', 'estado')) {
                $table->dropColumn('estado');
            }
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $columnas = array_filter([
                Schema::hasColumn('usuarios', 'empresa_id') ? 'empresa_id' : null,
                Schema::hasColumn('usuarios', 'estado_laboral') ? 'estado_laboral' : null,
                Schema::hasColumn('usuarios', 'carrera') ? 'carrera' : null,
                Schema::hasColumn('usuarios', 'estatus') ? 'estatus' : null,
            ]);

            if ($columnas !== []) {
                $table->dropColumn($columnas);
            }
        });
    }
};
