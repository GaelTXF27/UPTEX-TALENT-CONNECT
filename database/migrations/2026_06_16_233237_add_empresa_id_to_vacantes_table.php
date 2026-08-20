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
        Schema::table('vacantes', function (Blueprint $table) {
            // Relación con la tabla empresas
            // Se usa nullable() por si ya tienes vacantes registradas sin empresa asignada para que no marque error al migrar
            $table->foreignId('empresa_id')->nullable()->after('id')->constrained('empresas')->onDelete('cascade');

            // Nuevos campos para los filtros solicitados
            $table->string('categoria')->nullable()->after('descripcion');
            $table->string('experiencia')->nullable()->after('categoria');
            $table->string('tipo_contrato')->nullable()->after('tipo_jornada');
            $table->boolean('apto_discapacidad')->default(false)->after('tipo_contrato');

            // Campos para la vista detallada
            $table->text('requerimientos')->nullable();
            $table->text('aptitudes')->nullable();
            $table->string('salario_comisiones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacantes', function (Blueprint $table) {
            // Primero se elimina la llave foránea
            $table->dropForeign(['empresa_id']);
            
            // Luego se eliminan las columnas creadas en el método up()
            $table->dropColumn([
                'empresa_id',
                'categoria',
                'experiencia',
                'tipo_contrato',
                'apto_discapacidad',
                'requerimientos',
                'aptitudes',
                'salario_comisiones'
            ]);
        });
    }
};