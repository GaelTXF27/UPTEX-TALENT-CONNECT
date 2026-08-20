<?php

namespace App\Http\Controllers;

use App\Models\Postulacion;
use App\Models\Vacante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostulacionController extends Controller
{
    public function store(Request $request, Vacante $vacante)
    {
        $usuario = $request->user();

        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => [
                'required',
                'email',
                'max:255',
                Rule::unique('postulaciones', 'correo')->where('vacante_id', $vacante->id),
            ],
            'telefono' => 'nullable|string|max:30',
            'carrera' => 'nullable|string|max:255',
            'mensaje' => 'nullable|string|max:2000',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ], [
            'correo.unique' => 'Ya existe una postulacion con este correo para esta vacante.',
            'cv.mimes' => 'El CV debe ser un archivo PDF, DOC o DOCX.',
            'cv.max' => 'El CV no debe pesar mas de 5 MB.',
        ]);

        $rutaCv = $request->file('cv')->store('cvs', 'public');

        $postulacion = Postulacion::create([
            'vacante_id' => $vacante->id,
            'usuario_id' => $usuario?->id,
            'nombre' => $datos['nombre'],
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'] ?? null,
            'carrera' => $datos['carrera'] ?? $usuario?->carrera,
            'mensaje' => $datos['mensaje'] ?? null,
            'cv_path' => $rutaCv,
            'estado' => 'Pendiente',
        ]);

        return response()->json([
            'mensaje' => 'Postulacion enviada correctamente.',
            'postulacion' => $postulacion,
        ], 201);
    }
}
