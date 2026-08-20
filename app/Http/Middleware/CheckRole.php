<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    private function normalizarRol(?string $rol): string
    {
        $rol = strtolower(trim($rol ?? ''));

        return match ($rol) {
            'administrador', 'administrator' => 'admin',
            'compania', 'compañia', 'company' => 'empresa',
            'alumno', 'estudiante', 'usuario' => 'egresado',
            default => $rol,
        };
    }

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $usuario = $request->user();

        if (!$usuario) {
            return $request->expectsJson()
                ? response()->json(['mensaje' => 'No autenticado.'], 401)
                : redirect('/');
        }

        $rolUsuario = $this->normalizarRol($usuario->rol);
        $rolesPermitidos = array_map(fn ($rol) => $this->normalizarRol($rol), $roles);

        if (!in_array($rolUsuario, $rolesPermitidos, true)) {
            abort(403, 'No tienes permisos para acceder a esta seccion.');
        }

        return $next($request);
    }
}
