<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Configuracion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Pruebas de permisos: rutas de catálogos y módulos con permission:catalogos.* y modulos.*;
 * Rol Capturista sin acceso a usuarios ni configuración crítica; Administrador con acceso total.
 */
class PermissionsApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function asAdmin(): self
    {
        $admin = User::where('email', 'admin@greenpoint.com')->first();
        $this->assertNotNull($admin);
        Sanctum::actingAs($admin, ['*']);
        return $this;
    }

    private function asCapturista(): self
    {
        $capturista = User::create([
            'name' => 'Capturista Test',
            'email' => 'capturista@greenpoint.com',
            'password' => bcrypt('password'),
            'estatus' => 'activo',
        ]);
        $capturista->assignRole('Capturista');
        Sanctum::actingAs($capturista, ['*']);
        return $this;
    }

    /** @test */
    public function rutas_protegidas_requieren_autenticacion(): void
    {
        $this->getJson('/api/servicios')->assertStatus(401);
        $this->getJson('/api/pagina-nosotros')->assertStatus(401);
        $this->getJson('/api/users')->assertStatus(401);
        $this->getJson('/api/configuracion')->assertStatus(401);
    }

    /** @test */
    public function capturista_no_accede_a_usuarios(): void
    {
        $this->asCapturista();
        $this->getJson('/api/users')->assertStatus(403);
        $this->getJson('/api/users/all')->assertStatus(403);
    }

    /** @test */
    public function capturista_no_accede_a_configuracion_critica(): void
    {
        $this->asCapturista();
        $this->getJson('/api/configuracion')->assertStatus(403);
        Configuracion::firstOrCreate(['clave' => 'test'], ['valor' => 'x']);
        $this->putJson('/api/configuracion', ['items' => [['clave' => 'test', 'valor' => 'y']]])->assertStatus(403);
    }

    /** @test */
    public function capturista_accede_a_catalogos(): void
    {
        $this->asCapturista();
        $this->getJson('/api/servicios')->assertStatus(200);
        $this->getJson('/api/clientes')->assertStatus(200);
        $this->getJson('/api/galeria')->assertStatus(200);
        $this->getJson('/api/banners')->assertStatus(200);
        $this->getJson('/api/contactos')->assertStatus(200);
        $this->getJson('/api/redes-sociales')->assertStatus(200);
    }

    /** @test */
    public function capturista_accede_a_modulos_administrables(): void
    {
        $this->asCapturista();
        $this->getJson('/api/pagina-nosotros')->assertStatus(200);
        $this->getJson('/api/pagina-historia')->assertStatus(200);
        $this->getJson('/api/pagina-tecnologia')->assertStatus(200);
        $this->getJson('/api/pagina-aviso')->assertStatus(200);
    }

    /** @test */
    public function capturista_accede_a_formularios_contacto(): void
    {
        $this->asCapturista();
        $this->getJson('/api/formularios-contacto')->assertStatus(200);
    }

    /** @test */
    public function administrador_accede_a_usuarios_y_configuracion(): void
    {
        $this->asAdmin();
        $this->getJson('/api/users')->assertStatus(200);
        $this->getJson('/api/configuracion')->assertStatus(200);
    }

    /** @test */
    public function administrador_accede_a_catalogos_y_modulos(): void
    {
        $this->asAdmin();
        $this->getJson('/api/servicios')->assertStatus(200);
        $this->getJson('/api/pagina-nosotros')->assertStatus(200);
    }
}
