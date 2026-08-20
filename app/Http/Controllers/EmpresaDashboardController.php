<?php

namespace App\Http\Controllers;

use App\Models\Vacante;
use App\Models\Postulacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaDashboardController extends Controller
{
    private function empresaId(Request $request): ?int
    {
        return $request->user()?->empresa_id;
    }

    public function dashboard(Request $request)
    {
        $empresaId = $this->empresaId($request);

        if (!$empresaId) {
            return response()->json([
                'mensaje' => 'El usuario empresa no tiene una empresa asignada.',
            ], 422);
        }

        $vacantesEmpresa = Vacante::where('empresa_id', $empresaId)->get();
        $vacantesActivas = $vacantesEmpresa->where('activa', true)->count();
        $totalVacantes = $vacantesEmpresa->count();
        $vacanteIds = $vacantesEmpresa->pluck('id');
        $postulaciones = Postulacion::whereIn('vacante_id', $vacanteIds)->get();
        $totalPostulantes = $postulaciones->count();
        $contratados = $postulaciones->where('estado', 'Contratado')->count();

        $porCategoria = $vacantesEmpresa
            ->groupBy(fn (Vacante $vacante) => $vacante->categoria ?: ($vacante->sector ?: $vacante->puesto))
            ->map(fn ($items, $etiqueta) => ['etiqueta' => $etiqueta, 'total' => $items->count()])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $porMes = $vacantesEmpresa
            ->filter(fn (Vacante $vacante) => $vacante->created_at)
            ->groupBy(fn (Vacante $vacante) => $vacante->created_at->format('Y-m'))
            ->map(fn ($items, $orden) => [
                'orden' => $orden,
                'etiqueta' => $items->first()->created_at->format('M'),
                'total' => $items->count(),
            ])
            ->sortBy('orden')
            ->take(6)
            ->values();

        return response()->json([
            'metricas' => [
                'vacantes' => $vacantesActivas,
                'postulantes' => $totalPostulantes,
                'tasa' => $totalPostulantes > 0 ? round(($contratados / $totalPostulantes) * 100) : 0,
                'visitas' => $totalVacantes,
            ],
            'funnel' => [
                'pendientes' => $postulaciones->where('estado', 'Pendiente')->count(),
                'entrevista' => $postulaciones->where('estado', 'En Entrevista')->count(),
                'rechazados' => $postulaciones->where('estado', 'Rechazado')->count(),
                'contratados' => $contratados,
            ],
            'graficas' => [
                'perfiles' => [
                    'labels' => $porCategoria->pluck('etiqueta'),
                    'data' => $porCategoria->pluck('total'),
                ],
                'evolucion' => [
                    'labels' => $porMes->pluck('etiqueta'),
                    'data' => $porMes->pluck('total'),
                ],
            ],
        ]);
    }

    public function vacantes(Request $request)
    {
        $empresaId = $this->empresaId($request);

        if (!$empresaId) {
            return response()->json([]);
        }

        $vacantes = Vacante::where('empresa_id', $empresaId)
            ->withCount('postulaciones')
            ->latest()
            ->get()
            ->map(function (Vacante $vacante) {
                return [
                    'id' => $vacante->id,
                    'puesto' => $vacante->puesto,
                    'titulo' => $vacante->puesto,
                    'tipo' => $vacante->tipo_jornada,
                    'tipo_jornada' => $vacante->tipo_jornada,
                    'tipo_contrato' => $vacante->tipo_contrato,
                    'categoria' => $vacante->categoria,
                    'perfil' => $vacante->categoria ?: ($vacante->sector ?: 'Sin categoria'),
                    'postulantes' => $vacante->postulaciones_count,
                    'estado' => $vacante->activa ? 'Activa' : 'Cerrada',
                    'fecha' => optional($vacante->created_at)->diffForHumans(),
                    'ubicacion' => $vacante->ubicacion,
                    'descripcion' => $vacante->descripcion,
                    'requerimientos' => $vacante->requerimientos,
                    'aptitudes' => $vacante->aptitudes,
                    'salario' => $vacante->salario_ofrecido,
                ];
            });

        return response()->json($vacantes);
    }

    public function solicitantes(Request $request)
    {
        $empresaId = $this->empresaId($request);

        if (!$empresaId) {
            return response()->json([]);
        }

        $solicitantes = Postulacion::with('vacante')
            ->whereHas('vacante', fn ($query) => $query->where('empresa_id', $empresaId))
            ->latest()
            ->get()
            ->map(fn (Postulacion $postulacion) => [
                'id' => $postulacion->id,
                'nombre' => $postulacion->nombre,
                'correo' => $postulacion->correo,
                'telefono' => $postulacion->telefono,
                'carrera' => $postulacion->carrera ?: 'Sin carrera',
                'vacante' => $postulacion->vacante?->puesto ?: 'Vacante no disponible',
                'fecha' => optional($postulacion->created_at)->format('d/m/Y'),
                'estado' => $postulacion->estado,
                'cv_url' => Storage::url($postulacion->cv_path),
            ]);

        return response()->json($solicitantes);
    }

    public function perfil(Request $request)
    {
        $usuario = $request->user()->load('empresa');
        $empresa = $usuario->empresa;

        if (!$empresa) {
            return response()->json([
                'mensaje' => 'El usuario empresa no tiene una empresa asignada.',
            ], 422);
        }

        return response()->json([
            'empresa' => $empresa,
            'representante' => [
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
            ],
        ]);
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = $request->user()->load('empresa');
        $empresa = $usuario->empresa;

        if (!$empresa) {
            return response()->json([
                'mensaje' => 'El usuario empresa no tiene una empresa asignada.',
            ], 422);
        }

        $datos = $request->validate([
            'sector' => 'nullable|string|max:255',
            'web' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $empresa->update($datos);

        return response()->json([
            'mensaje' => 'Perfil actualizado correctamente.',
            'empresa' => $empresa->fresh(),
        ]);
    }

    public function publicarVacante(Request $request)
    {
        $empresaId = $this->empresaId($request);

        if (!$empresaId) {
            return response()->json([
                'mensaje' => 'El usuario empresa no tiene una empresa asignada.',
            ], 422);
        }

        $datos = $request->validate([
            'puesto' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'tipo_jornada' => 'required|string|max:255',
            'tipo_contrato' => 'nullable|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'salario_ofrecido' => 'nullable|numeric|min:0|max:99999999.99',
            'descripcion' => 'required|string',
            'requerimientos' => 'nullable|string',
            'aptitudes' => 'nullable|string',
        ]);

        $datos['empresa_id'] = $empresaId;
        $datos['empresa'] = $request->user()->empresa?->nombre ?? $request->user()->nombre;
        $datos['activa'] = true;

        $vacante = Vacante::create($datos);

        return response()->json([
            'mensaje' => 'Vacante publicada correctamente.',
            'vacante' => $vacante,
        ], 201);
    }

    public function actualizarVacante(Request $request, Vacante $vacante)
    {
        $empresaId = $this->empresaId($request);

        if (!$empresaId || (int) $vacante->empresa_id !== (int) $empresaId) {
            abort(403, 'No puedes editar esta vacante.');
        }

        $datos = $request->validate([
            'puesto' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'tipo_jornada' => 'required|string|max:255',
            'tipo_contrato' => 'nullable|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'salario_ofrecido' => 'nullable|numeric|min:0|max:99999999.99',
            'descripcion' => 'required|string',
            'requerimientos' => 'nullable|string',
            'aptitudes' => 'nullable|string',
        ]);

        $vacante->update($datos);

        return response()->json([
            'mensaje' => 'Vacante actualizada correctamente.',
            'vacante' => $vacante->fresh(),
        ]);
    }

    public function actualizarEstadoPostulacion(Request $request, Postulacion $postulacion)
    {
        $empresaId = $this->empresaId($request);
        $postulacion->load('vacante');

        if (!$empresaId || (int) $postulacion->vacante?->empresa_id !== (int) $empresaId) {
            abort(403, 'No puedes modificar esta postulacion.');
        }

        $datos = $request->validate([
            'estado' => 'required|in:Pendiente,En Entrevista,Rechazado,Contratado',
        ]);

        $transicionesPermitidas = [
            'Pendiente' => ['En Entrevista', 'Rechazado', 'Contratado'],
            'En Entrevista' => ['Rechazado', 'Contratado'],
            'Rechazado' => [],
            'Contratado' => [],
        ];

        if (!in_array($datos['estado'], $transicionesPermitidas[$postulacion->estado] ?? [], true)) {
            return response()->json([
                'mensaje' => 'Este estatus ya no permite esa accion.',
            ], 422);
        }

        $postulacion->update([
            'estado' => $datos['estado'],
        ]);

        return response()->json([
            'mensaje' => 'Estatus actualizado correctamente.',
            'postulacion' => [
                'id' => $postulacion->id,
                'estado' => $postulacion->estado,
            ],
        ]);
    }
}
