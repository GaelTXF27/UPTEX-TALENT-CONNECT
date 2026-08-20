<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postulacion extends Model
{
    use HasFactory;

    protected $table = 'postulaciones';

    protected $fillable = [
        'vacante_id',
        'usuario_id',
        'nombre',
        'correo',
        'telefono',
        'carrera',
        'mensaje',
        'cv_path',
        'estado',
    ];

    public function vacante()
    {
        return $this->belongsTo(Vacante::class, 'vacante_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
