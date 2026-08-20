<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    // Especificamos el nombre de la tabla (opcional pero buena práctica)
    protected $table = 'empresas';

    // Definimos los campos en los que se pueden insertar datos masivamente
    protected $fillable = [
        'nombre',
        'sector',
        'ubicacion',
        'direccion',
        'descripcion',
        'telefono',
        'email',
        'web',
        'estado'
    ];

    /**
     * Relación: Una empresa tiene muchas vacantes.
     */
    public function vacantes()
    {
        return $this->hasMany(Vacante::class);
    }
}
