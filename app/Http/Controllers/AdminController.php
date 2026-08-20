<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Postulacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Obtener lista completa de empresas ligadas a usuarios
    public function getEmpresas()
    {
        $empresas = User::where('rol', 'empresa')->get();
        return response()->json($empresas);
    }

    // Obtener lista de alumnos egresados
    public function getEgresados()
    {
        $egresados = User::where('rol', 'egresado')->get();
        return response()->json($egresados);
    }

    public function usuariosAceptados()
    {
        $egresados = User::where('rol', 'egresado')
            ->orderBy('nombre')
            ->get();

        $ids = $egresados->pluck('id')->filter()->values();
        $correos = $egresados->pluck('correo')
            ->filter()
            ->map(fn ($correo) => strtolower(trim($correo)))
            ->values();

        $postulaciones = Postulacion::with('vacante')
            ->where(function ($query) use ($ids, $correos) {
                $query->whereIn('usuario_id', $ids)
                    ->orWhereIn('correo', $correos);
            })
            ->latest()
            ->get();

        $prioridad = [
            'contratado' => 4,
            'en entrevista' => 3,
            'rechazado' => 2,
            'pendiente' => 1,
        ];

        $datos = $egresados->map(function (User $egresado) use ($postulaciones, $prioridad) {
            $correo = strtolower(trim($egresado->correo));
            $postulacion = $postulaciones
                ->filter(fn ($item) => (int) $item->usuario_id === (int) $egresado->id || strtolower(trim($item->correo)) === $correo)
                ->sort(function ($a, $b) use ($prioridad) {
                    $prioridadA = $prioridad[strtolower(trim($a->estado ?? ''))] ?? 0;
                    $prioridadB = $prioridad[strtolower(trim($b->estado ?? ''))] ?? 0;

                    if ($prioridadA !== $prioridadB) {
                        return $prioridadB <=> $prioridadA;
                    }

                    return (optional($b->updated_at)->timestamp ?? 0) <=> (optional($a->updated_at)->timestamp ?? 0);
                })
                ->first();

            return [
                'id' => $egresado->id,
                'nombre' => $egresado->nombre,
                'correo' => $egresado->correo,
                'carrera' => $egresado->carrera ?? $postulacion?->carrera ?? 'Sin carrera',
                'estatus' => $postulacion?->estado ?? 'Sin proceso',
                'vacante' => $postulacion?->vacante?->puesto ?? $postulacion?->vacante?->titulo ?? 'Sin vacante',
                'empresa' => $postulacion?->vacante?->empresa ?? 'Sin empresa',
                'fecha' => optional($postulacion?->updated_at)->format('Y-m-d') ?? 'Sin fecha',
            ];
        });

        return response()->json($datos->values());
    }

    // Registrar un nuevo representante vinculado a una empresa
    public function crearRepresentante(Request $request)
    {
        try {
            // 1. Validar la petición incluyendo el ID de la empresa
            $request->validate([
                'nombre' => 'required|string|max:255',
                'correo' => 'required|string|email|max:255|unique:usuarios,correo',
                'contrasena' => 'required|string|min:6',
                'empresa_id' => 'nullable|exists:empresas,id',
                'empresa_nombre' => 'required_without:empresa_id|string|max:255'
            ]);

            $empresaId = $request->empresa_id;

            if (!$empresaId) {
                $empresa = Empresa::firstOrCreate(
                    ['nombre' => trim($request->empresa_nombre)],
                    ['estado' => 'activa']
                );

                $empresaId = $empresa->id;
            }

            $user = User::create([
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'contrasena' => Hash::make($request->contrasena),
                'rol' => 'empresa',
                'empresa_id' => $empresaId
            ]);

            return response()->json(['success' => true, 'user' => $user], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejo específico para errores de validación (ej. la empresa no existe o correo duplicado)
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Manejo de errores generales del servidor
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Editar o actualizar estado/datos de un usuario
    public function actualizarUsuario(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:usuarios,correo,'.$id,
            'estatus' => 'nullable|string'
        ]);

        $user->nombre = $request->nombre;
        $user->correo = $request->correo;
        if($request->has('estatus')) {
            $user->estatus = $request->estatus;
        }
        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }
}
