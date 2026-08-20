<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_use_bearer_token_on_admin_routes(): void
    {
        $correo = 'test-admin-login@example.com';

        User::where('correo', $correo)->delete();

        $usuario = User::create([
            'nombre' => 'Admin Temporal',
            'correo' => $correo,
            'contrasena' => Hash::make('secret123'),
            'rol' => 'admin',
        ]);

        try {
            $login = $this->postJson('/api/login', [
                'correo' => $correo,
                'contrasena' => 'secret123',
            ]);

            $login->assertOk()
                ->assertJsonPath('usuario.rol', 'admin')
                ->assertJsonStructure(['token']);

            $token = $login->json('token');

            $this->withHeader('Authorization', 'Bearer '.$token)
                ->getJson('/api/admin/egresados')
                ->assertOk();

            $this->withHeader('Authorization', 'Bearer '.$token)
                ->postJson('/api/logout')
                ->assertOk();

            $this->assertSame(0, $usuario->tokens()->count());
        } finally {
            $usuario->tokens()->delete();
            $usuario->delete();
        }
    }

    public function test_login_normalizes_email_before_authentication(): void
    {
        $usuario = User::create([
            'nombre' => 'Admin Alias',
            'correo' => 'AdminAlias@Test.com',
            'contrasena' => Hash::make('secret123'),
            'rol' => 'admin',
        ]);

        try {
            $login = $this->postJson('/api/login', [
                'correo' => ' adminalias@test.com ',
                'contrasena' => 'secret123',
            ]);

            $login->assertOk()
                ->assertJsonPath('usuario.rol', 'admin')
                ->assertJsonStructure(['token']);

            $this->withHeader('Authorization', 'Bearer '.$login->json('token'))
                ->getJson('/api/admin/egresados')
                ->assertOk();
        } finally {
            $usuario->tokens()->delete();
            $usuario->delete();
        }
    }

    public function test_registration_normalizes_administrator_role_alias(): void
    {
        $response = $this->postJson('/api/registro', [
            'nombre' => 'Admin Registrado',
            'correo' => 'registro-admin@test.com',
            'contrasena' => 'Secret123!',
            'rol' => 'administrador',
        ]);

        $response->assertCreated()
            ->assertJsonPath('usuario.rol', 'admin');

        $this->assertDatabaseHas('usuarios', [
            'correo' => 'registro-admin@test.com',
            'rol' => 'admin',
        ]);
    }

    public function test_registration_rejects_weak_passwords(): void
    {
        $this->postJson('/api/registro', [
            'nombre' => 'Egresado Debil',
            'correo' => 'debil@test.com',
            'contrasena' => 'secret123',
            'rol' => 'egresado',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['contrasena']);
    }
}
