<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacante extends Model
{
    use HasFactory;

    protected $table = 'vacantes';

    protected $fillable = [
        'puesto',
        'empresa',
        'empresa_id', // Cambiado de 'empresa' a 'empresa_id' para la relación
        'tipo_jornada',
        'ubicacion',
        'descripcion',
        'activa',
        'sector',
        'salario_ofrecido',
        // Nuevos campos agregados:
        'categoria',
        'experiencia',
        'tipo_contrato',
        'apto_discapacidad',
        'requerimientos',
        'aptitudes',
        'salario_comisiones'
    ];

    /**
     * Relación: Una vacante pertenece a una empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function postulaciones()
    {
        return $this->hasMany(Postulacion::class, 'vacante_id');
    }
}
