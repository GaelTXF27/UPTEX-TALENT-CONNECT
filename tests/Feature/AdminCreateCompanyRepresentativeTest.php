<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Postulacion;
use App\Models\User;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCreateCompanyRepresentativeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_representative_with_new_company(): void
    {
        $admin = User::create([
            'nombre' => 'Admin',
            'correo' => 'admin@test.com',
            'contrasena' => 'password',
            'rol' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/crear-representante', [
            'nombre' => 'Contacto Empresa',
            'correo' => 'contacto@empresa.test',
            'contrasena' => 'password',
            'empresa_nombre' => 'Nueva Empresa Test',
        ])->assertCreated();

        $empresa = Empresa::where('nombre', 'Nueva Empresa Test')->first();

        $this->assertNotNull($empresa);
        $this->assertDatabaseHas('usuarios', [
            'correo' => 'contacto@empresa.test',
            'rol' => 'empresa',
            'empresa_id' => $empresa->id,
        ]);
    }

    public function test_admin_can_list_egresados_by_process_status(): void
    {
        $admin = User::create([
            'nombre' => 'Admin Test',
            'correo' => 'admin-status@test.com',
            'contrasena' => 'password',
            'rol' => 'admin',
        ]);

        $egresado = User::create([
            'nombre' => 'Egresado Contratado',
            'correo' => 'egresado-status@test.com',
            'contrasena' => 'password',
            'rol' => 'egresado',
            'carrera' => 'Ingenieria',
        ]);

        $empresa = Empresa::create(['nombre' => 'Empresa Status']);
        $vacante = Vacante::create([
            'empresa_id' => $empresa->id,
            'empresa' => $empresa->nombre,
            'puesto' => 'Analista de Datos',
            'tipo_jornada' => 'Presencial',
            'ubicacion' => 'Texcoco',
            'descripcion' => 'Analisis de datos',
            'activa' => true,
        ]);

        Postulacion::create([
            'vacante_id' => $vacante->id,
            'usuario_id' => $egresado->id,
            'nombre' => $egresado->nombre,
            'correo' => $egresado->correo,
            'carrera' => 'Ingenieria',
            'cv_path' => 'cvs/test.pdf',
            'estado' => 'Contratado',
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/usuarios-aceptados')
            ->assertOk()
            ->assertJsonFragment([
                'nombre' => 'Egresado Contratado',
                'estatus' => 'Contratado',
                'vacante' => 'Analista de Datos',
                'empresa' => 'Empresa Status',
            ]);
    }
}
