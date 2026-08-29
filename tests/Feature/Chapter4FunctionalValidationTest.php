<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Vacante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Chapter4FunctionalValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_rejects_invalid_password(): void
    {
        User::create([
            'nombre' => 'Usuario Login',
            'correo' => 'login-error@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'egresado',
        ]);

        $this->postJson('/api/login', [
            'correo' => 'login-error@test.com',
            'contrasena' => 'incorrecta',
        ])->assertUnauthorized()
            ->assertJsonPath('mensaje', 'Correo o contraseña incorrectos.');
    }

    public function test_registration_rejects_required_fields_and_duplicate_email(): void
    {
        User::create([
            'nombre' => 'Usuario Existente',
            'correo' => 'duplicado@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'egresado',
        ]);

        $this->postJson('/api/registro', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre', 'correo', 'contrasena']);

        $this->postJson('/api/registro', [
            'nombre' => 'Usuario Duplicado',
            'correo' => 'duplicado@test.com',
            'contrasena' => 'Secret123!',
            'rol' => 'egresado',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['correo']);
    }

    public function test_protected_endpoints_reject_missing_token_and_wrong_roles(): void
    {
        $empresa = Empresa::create(['nombre' => 'Empresa Rol']);

        $usuarioEmpresa = User::create([
            'nombre' => 'Empresa Rol',
            'correo' => 'empresa-rol@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'empresa',
            'empresa_id' => $empresa->id,
        ]);

        $egresado = User::create([
            'nombre' => 'Egresado Rol',
            'correo' => 'egresado-rol@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'egresado',
        ]);

        $this->getJson('/api/admin/egresados')->assertUnauthorized();

        Sanctum::actingAs($usuarioEmpresa);
        $this->getJson('/api/admin/egresados')->assertForbidden();

        Sanctum::actingAs($egresado);
        $this->getJson('/api/empresa/dashboard')->assertForbidden();
    }

    public function test_public_vacantes_endpoint_returns_only_active_filtered_records(): void
    {
        $empresa = Empresa::create(['nombre' => 'Empresa Publica']);

        Vacante::create([
            'empresa_id' => $empresa->id,
            'empresa' => $empresa->nombre,
            'puesto' => 'Desarrollador Backend',
            'tipo_jornada' => 'Remoto',
            'descripcion' => 'Vacante activa visible.',
            'activa' => true,
        ]);

        Vacante::create([
            'empresa_id' => $empresa->id,
            'empresa' => $empresa->nombre,
            'puesto' => 'Disenador UI',
            'tipo_jornada' => 'Presencial',
            'descripcion' => 'Vacante cerrada no visible.',
            'activa' => false,
        ]);

        $this->getJson('/api/vacantes?search=Backend&jornada=Remoto')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['puesto' => 'Desarrollador Backend'])
            ->assertJsonMissing(['puesto' => 'Disenador UI']);
    }

    public function test_dashboard_metrics_are_calculated_from_database_records(): void
    {
        $admin = User::create([
            'nombre' => 'Admin Metricas',
            'correo' => 'admin-metricas@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'admin',
        ]);

        User::create([
            'nombre' => 'Egresado Contratado',
            'correo' => 'contratado@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'egresado',
            'estado_laboral' => 'contratado',
            'carrera' => 'Ingenieria en Software',
        ]);

        User::create([
            'nombre' => 'Egresado Buscando',
            'correo' => 'buscando@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'egresado',
            'estado_laboral' => 'buscando',
            'carrera' => 'Ingenieria Industrial',
        ]);

        $empresa = Empresa::create([
            'nombre' => 'Empresa Activa',
            'estado' => 'activa',
        ]);

        Vacante::create([
            'empresa_id' => $empresa->id,
            'empresa' => $empresa->nombre,
            'puesto' => 'Analista',
            'tipo_jornada' => 'Remoto',
            'descripcion' => 'Vacante con salario.',
            'activa' => true,
            'salario_ofrecido' => 15000,
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/dashboard/metricas')
            ->assertOk()
            ->assertJsonPath('exito', true)
            ->assertJsonPath('kpis.empleabilidad', 50)
            ->assertJsonPath('kpis.salario_promedio', '15,000.00')
            ->assertJsonPath('kpis.empresas_activas', 1);
    }

    public function test_password_recovery_generates_token_and_resets_password(): void
    {
        $usuario = User::create([
            'nombre' => 'Usuario Recuperacion',
            'correo' => 'recuperacion@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'egresado',
        ]);

        $this->postJson('/api/solicitar-recuperacion', [
            'correo' => 'recuperacion@test.com',
        ])->assertOk()
            ->assertJsonPath('exito', true);

        $token = DB::table('tokens_restablecimiento_password')
            ->where('correo', 'recuperacion@test.com')
            ->value('token');

        $this->assertNotEmpty($token);

        $this->postJson('/api/restablecer-contrasena', [
            'correo' => 'recuperacion@test.com',
            'token' => $token,
            'nueva_contrasena' => 'Nueva123!',
        ])->assertOk()
            ->assertJsonPath('mensaje', 'Contraseña actualizada correctamente');

        $usuario->refresh();

        $this->assertTrue(Hash::check('Nueva123!', $usuario->contrasena));
        $this->assertDatabaseMissing('tokens_restablecimiento_password', [
            'correo' => 'recuperacion@test.com',
        ]);
    }

    public function test_postulation_rejects_invalid_cv_type_and_duplicate_application(): void
    {
        Storage::fake('public');

        $empresa = Empresa::create(['nombre' => 'Empresa Postulacion']);
        $vacante = Vacante::create([
            'empresa_id' => $empresa->id,
            'empresa' => $empresa->nombre,
            'puesto' => 'Soporte Tecnico',
            'tipo_jornada' => 'Presencial',
            'descripcion' => 'Vacante para soporte.',
            'activa' => true,
        ]);

        $egresado = User::create([
            'nombre' => 'Egresado CV',
            'correo' => 'egresado-cv@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'egresado',
        ]);

        Sanctum::actingAs($egresado);

        $this->post("/api/vacantes/{$vacante->id}/postular", [
            'nombre' => 'Egresado CV',
            'correo' => 'egresado-cv@test.com',
            'cv' => UploadedFile::fake()->create('cv.txt', 5, 'text/plain'),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cv']);

        $this->post("/api/vacantes/{$vacante->id}/postular", [
            'nombre' => 'Egresado CV',
            'correo' => 'egresado-cv@test.com',
            'cv' => UploadedFile::fake()->create('cv.pdf', 5, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertCreated();

        $this->post("/api/vacantes/{$vacante->id}/postular", [
            'nombre' => 'Egresado CV',
            'correo' => 'egresado-cv@test.com',
            'cv' => UploadedFile::fake()->create('cv.pdf', 5, 'application/pdf'),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['correo']);
    }

    public function test_company_cannot_modify_another_company_vacancy(): void
    {
        $empresaPropietaria = Empresa::create(['nombre' => 'Empresa Propietaria']);
        $empresaAjena = Empresa::create(['nombre' => 'Empresa Ajena']);

        $usuarioAjeno = User::create([
            'nombre' => 'Representante Ajeno',
            'correo' => 'ajeno@test.com',
            'contrasena' => Hash::make('Secret123!'),
            'rol' => 'empresa',
            'empresa_id' => $empresaAjena->id,
        ]);

        $vacante = Vacante::create([
            'empresa_id' => $empresaPropietaria->id,
            'empresa' => $empresaPropietaria->nombre,
            'puesto' => 'Vacante Propia',
            'tipo_jornada' => 'Remoto',
            'descripcion' => 'No debe modificarse por otra empresa.',
            'activa' => true,
        ]);

        Sanctum::actingAs($usuarioAjeno);

        $this->putJson("/api/empresa/vacantes/{$vacante->id}", [
            'puesto' => 'Cambio no permitido',
            'tipo_jornada' => 'Remoto',
            'descripcion' => 'Intento de cambio.',
        ])->assertForbidden();

        $this->assertDatabaseHas('vacantes', [
            'id' => $vacante->id,
            'puesto' => 'Vacante Propia',
        ]);
    }
}
