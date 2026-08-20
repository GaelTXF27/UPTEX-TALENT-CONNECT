<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostulacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_egresado_can_apply_to_vacante_with_cv(): void
    {
        Storage::fake('public');

        $empresa = Empresa::create(['nombre' => 'Empresa CV']);

        $vacante = Vacante::create([
            'empresa_id' => $empresa->id,
            'empresa' => $empresa->nombre,
            'puesto' => 'Analista de Datos',
            'tipo_jornada' => 'Remoto',
            'descripcion' => 'Analisis de datos operativos.',
            'activa' => true,
            'salario_ofrecido' => 18000,
        ]);

        $egresado = User::create([
            'nombre' => 'Egresado Postulante',
            'correo' => 'egresado@test.com',
            'contrasena' => 'password',
            'rol' => 'egresado',
            'carrera' => 'Ingenieria en Software',
        ]);

        Sanctum::actingAs($egresado);

        $response = $this->post("/api/vacantes/{$vacante->id}/postular", [
            'nombre' => 'Egresado Postulante',
            'correo' => 'egresado@test.com',
            'telefono' => '5551234567',
            'carrera' => 'Ingenieria en Software',
            'mensaje' => 'Me interesa la vacante.',
            'cv' => UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf'),
        ], ['Accept' => 'application/json']);

        $response->assertCreated()
            ->assertJsonPath('mensaje', 'Postulacion enviada correctamente.');

        $this->assertDatabaseHas('postulaciones', [
            'vacante_id' => $vacante->id,
            'usuario_id' => $egresado->id,
            'correo' => 'egresado@test.com',
            'estado' => 'Pendiente',
        ]);

        Storage::disk('public')->assertExists($response->json('postulacion.cv_path'));
    }
}
