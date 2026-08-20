<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecuperarPasswordMail;

class AuthController extends Controller
{
    private function normalizarRol(?string $rol): string
    {
        $rol = strtolower(trim($rol ?? 'egresado'));

        return match ($rol) {
            'administrador', 'administrator' => 'admin',
            'compania', 'compañia', 'company' => 'empresa',
            'alumno', 'estudiante', 'usuario' => 'egresado',
            default => in_array($rol, ['admin', 'empresa', 'egresado'], true) ? $rol : 'egresado',
        };
    }

    // Función para crear un usuario nuevo
    public function registro(Request $request)
    {
        // 1. Validar que nos envíen los datos correctos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|unique:App\Models\User,correo',
            'contrasena' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
        ], [
            'correo.unique' => 'Este correo ya está registrado.'
        ]);

        // 2. Crear el usuario en la base de datos
        $usuario = User::create([
            'nombre' => $request->nombre,
            'correo' => strtolower(trim($request->correo)),
            'contrasena' => Hash::make($request->contrasena),
            'rol' => $this->normalizarRol($request->rol),
        ]);

        // Se mantiene el token para futuras APIs o aplicación móvil
        $token = $usuario->createToken('token_acceso')->plainTextToken;

        return response()->json([
            'mensaje' => 'Usuario creado exitosamente',
            'token' => $token,
            'usuario' => $usuario
        ], 201);
    }

    // ==========================================================
    // LOGIN (CORREGIDO)
    // ==========================================================
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required'
        ]);

        $correo = strtolower(trim($request->correo));
        $usuario = User::whereRaw('LOWER(TRIM(correo)) = ?', [$correo])->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->contrasena)) {
            return response()->json([
                'mensaje' => 'Correo o contraseña incorrectos.'
            ], 401);
        }

        $rolNormalizado = $this->normalizarRol($usuario->rol);

        if ($usuario->correo !== $correo || $usuario->rol !== $rolNormalizado) {
            $usuario->forceFill([
                'correo' => $correo,
                'rol' => $rolNormalizado,
            ])->save();
        }

        // Eliminar tokens anteriores (opcional)
        $usuario->tokens()->delete();

        // Crear nuevo token
        $token = $usuario->createToken('token_acceso')->plainTextToken;

        return response()->json([
            'mensaje' => 'Inicio de sesión exitoso.',
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $rolNormalizado
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()?->tokens()->delete();

        return response()->json([
            'mensaje' => 'Sesion cerrada correctamente.'
        ]);
    }

    // Función para solicitar recuperación
    public function solicitarRecuperacion(Request $request)
    {
        $request->validate([
            'correo' => 'required|email'
        ]);

        $usuario = User::where('correo', $request->correo)->first();

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'El correo no existe en el sistema.'
            ], 404);
        }

        $token = Str::random(6);

        DB::table('tokens_restablecimiento_password')->updateOrInsert(
            ['correo' => $request->correo],
            ['token' => $token, 'creado_en' => now()]
        );

        try {

            Mail::to($request->correo)->send(new RecuperarPasswordMail($token));

            return response()->json([
                'exito' => true,
                'mensaje' => 'El código de recuperación ha sido enviado a tu correo electrónico.'
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'exito' => false,
                'mensaje' => 'No se pudo enviar el correo. Verifica la configuración.',
                'error_demo' => $e->getMessage()
            ], 500);

        }
    }

    // Guardar nueva contraseña
    public function restablecerContrasena(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'token' => 'required|string',
            'nueva_contrasena' => 'required|string|min:6'
        ]);

        $usuario = User::where('correo', $request->correo)->first();

        if (!$usuario) {
            return response()->json([
                'mensaje' => 'El correo no existe en el sistema.'
            ], 404);
        }

        $registro = DB::table('tokens_restablecimiento_password')
            ->where('correo', $request->correo)
            ->where('token', $request->token)
            ->first();

        if (!$registro) {
            return response()->json([
                'mensaje' => 'El código es inválido o ha expirado.'
            ], 400);
        }

        $usuario->contrasena = Hash::make($request->nueva_contrasena);
        $usuario->save();

        DB::table('tokens_restablecimiento_password')
            ->where('correo', $request->correo)
            ->delete();

        return response()->json([
            'mensaje' => 'Contraseña actualizada correctamente'
        ], 200);
    }
}
