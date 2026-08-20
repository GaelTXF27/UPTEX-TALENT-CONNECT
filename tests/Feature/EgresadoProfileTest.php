<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EgresadoProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_egresado_can_update_profile_and_photo(): void
    {
        Storage::fake('public');

        $usuario = User::create([
            'nombre' => 'Egresado Original',
            'correo' => 'egresado@test.com',
            'contrasena' => 'password',
            'rol' => 'egresado',
        ]);

        Sanctum::actingAs($usuario);

        $this->postJson('/api/egresado/perfil', [
            'nombre' => 'Egresado Actualizado',
            'carrera' => 'Ingenieria en Software',
            'estado_laboral' => 'buscando',
            'foto' => UploadedFile::fake()->createWithContent(
                'perfil.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
            ),
        ])
            ->assertOk()
            ->assertJsonPath('usuario.nombre', 'Egresado Actualizado')
            ->assertJsonPath('usuario.carrera', 'Ingenieria en Software');

        $usuario->refresh();

        $this->assertSame('Egresado Actualizado', $usuario->nombre);
        $this->assertNotNull($usuario->foto_perfil);
        Storage::disk('public')->assertExists($usuario->foto_perfil);
    }
}
