<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        // Iniciamos la consulta, incluyendo el conteo de vacantes relacionadas
        $query = Empresa::withCount('vacantes');

        // Filtro por búsqueda (nombre, sector)
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('sector', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por sector exacto
        if ($request->has('sector')) {
            $query->where('sector', $request->sector);
        }

        // Filtro por ubicación
        if ($request->has('ubicacion')) {
            $query->where('ubicacion', 'like', '%' . $request->ubicacion . '%');
        }

        // Devolvemos el JSON a la vista
        return response()->json($query->get());
    }
}