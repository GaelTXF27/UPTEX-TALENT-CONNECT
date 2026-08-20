<?php

namespace App\Http\Controllers; // CORREGIDO: Ahora usa contrabarra (\)

use App\Models\Vacante;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Asegura la importación del controlador base

class VacanteController extends Controller
{
    public function index(Request $request)
    {
        // Iniciamos la consulta filtrando solo las vacantes activas
        $query = Vacante::query()
            ->leftJoin('empresas', 'vacantes.empresa_id', '=', 'empresas.id')
            ->where('vacantes.activa', true)
            ->select('vacantes.*')
            ->selectRaw("COALESCE(NULLIF(vacantes.empresa, ''), empresas.nombre, 'Empresa no especificada') as empresa");

        // Filtro por palabra clave (puesto, empresa o descripción)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('vacantes.puesto', 'LIKE', "%{$search}%")
                  ->orWhere('vacantes.empresa', 'LIKE', "%{$search}%")
                  ->orWhere('empresas.nombre', 'LIKE', "%{$search}%")
                  ->orWhere('vacantes.descripcion', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo de jornada
        if ($request->filled('jornada')) {
            $query->where('vacantes.tipo_jornada', $request->input('jornada'));
        }

        // Ordenamos por las más recientes primero y obtenemos los registros
        $vacantes = $query->orderBy('vacantes.created_at', 'desc')->get();

        // Retornamos la respuesta en formato JSON
        return response()->json($vacantes, 200);
    }
}
