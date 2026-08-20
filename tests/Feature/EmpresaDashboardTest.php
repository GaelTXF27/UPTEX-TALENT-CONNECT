<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Postulacion;
use App\Models\User;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmpresaDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_empresa_can_use_real_dashboard_endpoints(): void
    {
        $empresa = Empresa::create([
            'nombre' => 'Empresa Real',
            'sector' => 'Tecnologias de la Informacion',
            'email' => 'rh@empresa.test',
        ]);

        $usuario = User::create([
            'nombre' => 'Representante Empresa',
            'correo' => 'empresa@test.com',
            'contrasena' => 'password',
            'rol' => 'empresa',
            'empresa_id' => $empresa->id,
        ]);

        Sanctum::actingAs($usuario);

        $this->getJson('/api/empresa/perfil')
            ->assertOk()
            ->assertJsonPath('empresa.nombre', 'Empresa Real');

        $createResponse = $this->postJson('/api/empresa/vacantes', [
            'puesto' => 'Desarrollador Laravel',
            'tipo_jornada' => 'Remoto',
            'tipo_contrato' => 'Tiempo Completo',
            'categoria' => 'Ingenieria en Software',
            'salario_ofrecido' => 15000,
            'descripcion' => 'Desarrollo y mantenimiento de aplicaciones web.',
            'requerimientos' => 'Laravel, MySQL y Git.',
            'aptitudes' => 'Trabajo en equipo y comunicacion.',
        ])->assertCreated();

        $vacanteId = $createResponse->json('vacante.id');

        $this->putJson("/api/empresa/vacantes/{$vacanteId}", [
            'puesto' => 'Desarrollador Laravel Senior',
            'tipo_jornada' => 'Presencial',
            'tipo_contrato' => 'Tiempo Completo',
            'categoria' => 'Ingenieria en Software',
            'salario_ofrecido' => 18000,
            'descripcion' => 'Desarrollo avanzado de aplicaciones web.',
            'requerimientos' => 'Laravel, MySQL, Git y APIs REST.',
            'aptitudes' => 'Liderazgo y comunicacion.',
        ])->assertOk();

        $this->getJson('/api/empresa/vacantes')
            ->assertOk()
            ->assertJsonFragment([
                'titulo' => 'Desarrollador Laravel Senior',
                'salario' => 18000,
                'requerimientos' => 'Laravel, MySQL, Git y APIs REST.',
                'aptitudes' => 'Liderazgo y comunicacion.',
            ]);

        $this->getJson('/api/empresa/dashboard')
            ->assertOk()
            ->assertJsonPath('metricas.vacantes', 1);
    }

    public function test_empresa_can_update_own_applicant_status(): void
    {
        $empresa = Empresa::create([
            'nombre' => 'Empresa Status',
        ]);

        $usuario = User::create([
            'nombre' => 'Representante Status',
            'correo' => 'status@test.com',
            'contrasena' => 'password',
            'rol' => 'empresa',
            'empresa_id' => $empresa->id,
        ]);

        $vacante = Vacante::create([
            'puesto' => 'Analista de Datos',
            'empresa' => $empresa->nombre,
            'empresa_id' => $empresa->id,
            'tipo_jornada' => 'Remoto',
            'descripcion' => 'Analisis de informacion.',
            'activa' => true,
        ]);

        $postulacion = Postulacion::create([
            'vacante_id' => $vacante->id,
            'nombre' => 'Candidato Prueba',
            'correo' => 'candidato@test.com',
            'carrera' => 'Ingenieria en Software',
            'cv_path' => 'cvs/candidato.pdf',
            'estado' => 'Pendiente',
        ]);

        Sanctum::actingAs($usuario);

        $this->putJson("/api/empresa/postulaciones/{$postulacion->id}/estado", [
            'estado' => 'En Entrevista',
        ])
            ->assertOk()
            ->assertJsonPath('postulacion.estado', 'En Entrevista');

        $this->assertDatabaseHas('postulaciones', [
            'id' => $postulacion->id,
            'estado' => 'En Entrevista',
        ]);

        $this->getJson('/api/empresa/solicitantes')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $postulacion->id,
                'estado' => 'En Entrevista',
            ]);

        $this->putJson("/api/empresa/postulaciones/{$postulacion->id}/estado", [
            'estado' => 'En Entrevista',
        ])->assertStatus(422);

        $this->putJson("/api/empresa/postulaciones/{$postulacion->id}/estado", [
            'estado' => 'Contratado',
        ])->assertOk();

        $this->putJson("/api/empresa/postulaciones/{$postulacion->id}/estado", [
            'estado' => 'Rechazado',
        ])->assertStatus(422);
    }
}
