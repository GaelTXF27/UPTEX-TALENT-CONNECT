<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function perfilEgresado(Request $request)
    {
        $usuario = $request->user();

        return response()->json([
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'correo' => $usuario->correo,
            'carrera' => $usuario->carrera,
            'estado_laboral' => $usuario->estado_laboral,
            'foto_perfil' => $usuario->foto_perfil,
            'foto_url' => $usuario->foto_perfil ? Storage::url($usuario->foto_perfil) : null,
        ]);
    }

    public function actualizarPerfilEgresado(Request $request)
    {
        $usuario = $request->user();

        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'carrera' => 'nullable|string|max:255',
            'estado_laboral' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $usuario->nombre = $datos['nombre'];
        $usuario->carrera = $datos['carrera'] ?? $usuario->carrera;
        $usuario->estado_laboral = $datos['estado_laboral'] ?? $usuario->estado_laboral;

        if ($request->hasFile('foto')) {
            if ($usuario->foto_perfil) {
                Storage::disk('public')->delete($usuario->foto_perfil);
            }

            $usuario->foto_perfil = $request->file('foto')->store('perfiles', 'public');
        }

        $usuario->save();

        return response()->json([
            'mensaje' => 'Perfil actualizado correctamente.',
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'carrera' => $usuario->carrera,
                'estado_laboral' => $usuario->estado_laboral,
                'foto_url' => $usuario->foto_perfil ? Storage::url($usuario->foto_perfil) : null,
            ],
        ]);
    }

    public function obtenerMetricas(Request $request)
    {
        try {
            $totalEgresados = DB::table('usuarios')->where('rol', 'egresado')->count();
            $egresadosContratados = DB::table('usuarios')
                ->where('rol', 'egresado')
                ->where('estado_laboral', 'contratado')
                ->count();

            $tasaEmpleabilidad = $totalEgresados > 0
                ? round(($egresadosContratados / $totalEgresados) * 100)
                : 0;

            $empresasActivas = DB::table('empresas')->where('estado', 'activa')->count();
            $salarioPromedio = DB::table('vacantes')->avg('salario_ofrecido') ?? 0;

            $carreras = DB::table('usuarios')
                ->select('carrera', DB::raw('count(*) as total'))
                ->where('rol', 'egresado')
                ->where('estado_laboral', 'contratado')
                ->whereNotNull('carrera')
                ->groupBy('carrera')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $empresasMasActivas = DB::table('empresas')
                ->leftJoin('vacantes', function ($join) {
                    $join->on('empresas.id', '=', 'vacantes.empresa_id')
                        ->where('vacantes.activa', true);
                })
                ->select('empresas.nombre', DB::raw('count(vacantes.id) as total'))
                ->groupBy('empresas.id', 'empresas.nombre')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $vacantesMasSolicitadas = DB::table('vacantes')
                ->select('puesto', DB::raw('count(*) as total'))
                ->whereNotNull('puesto')
                ->groupBy('puesto')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $empresaTop = $empresasMasActivas->first();

            return response()->json([
                'exito' => true,
                'kpis' => [
                    'empleabilidad' => $tasaEmpleabilidad,
                    'tiempo_promedio' => 18,
                    'salario_promedio' => number_format($salarioPromedio, 2),
                    'empresas_activas' => $empresasActivas,
                    'empresa_top_nombre' => $empresaTop->nombre ?? 'Sin datos',
                    'empresa_top_total' => $empresaTop->total ?? 0,
                ],
                'graficas' => [
                    'carreras' => [
                        'etiquetas' => $carreras->pluck('carrera'),
                        'valores' => $carreras->pluck('total'),
                    ],
                    'empresas' => [
                        'etiquetas' => $empresasMasActivas->pluck('nombre'),
                        'valores' => $empresasMasActivas->pluck('total'),
                    ],
                    'vacantes' => [
                        'etiquetas' => $vacantesMasSolicitadas->pluck('puesto'),
                        'valores' => $vacantesMasSolicitadas->pluck('total'),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'No se pudieron calcular las metricas del dashboard.',
                'kpis' => [
                    'empleabilidad' => 0,
                    'tiempo_promedio' => 0,
                    'salario_promedio' => '0.00',
                    'empresas_activas' => 0,
                    'empresa_top_nombre' => 'Sin datos',
                    'empresa_top_total' => 0,
                ],
                'graficas' => [
                    'carreras' => ['etiquetas' => ['Sin datos'], 'valores' => [0]],
                    'empresas' => ['etiquetas' => ['Sin datos'], 'valores' => [0]],
                    'vacantes' => ['etiquetas' => ['Sin datos'], 'valores' => [0]],
                ],
            ]);
        }
    }
}
