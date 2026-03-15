<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PaginaNosotros;
use App\Models\PaginaHistoria;
use App\Models\PaginaTecnologia;
use App\Models\PaginaAviso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Pruebas de CRUD de módulos administrables (Nosotros, Historia, Tecnología, Aviso) vía API.
 * show/update con estructura completa; acceso protegido con permission:modulos.*
 */
class ModulosAdministrablesApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->admin = User::where('email', 'admin@greenpoint.com')->first();
        $this->assertNotNull($this->admin, 'Usuario admin debe existir tras el seeder');
    }

    private function authRequest(): self
    {
        Sanctum::actingAs($this->admin, ['*']);
        return $this;
    }

    /** @test */
    public function pagina_nosotros_show_devuelve_estructura_y_permite_crear_si_no_existe(): void
    {
        $this->authRequest();
        $response = $this->getJson('/api/pagina-nosotros');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'titulo', 'estado', 'imagenes', 'progreso',
        ]);
        $this->assertIsArray($response->json('imagenes'));
        $this->assertIsArray($response->json('progreso'));
    }

    /** @test */
    public function pagina_nosotros_update_acepta_estructura_completa(): void
    {
        PaginaNosotros::query()->delete();
        $this->authRequest();
        $payload = [
            'titulo' => 'Quiénes somos',
            'subtitulo' => 'Subtítulo',
            'texto_descriptivo' => '<p>Texto</p>',
            'texto_adicional' => null,
            'url_video' => null,
            'meta_descripcion' => 'Meta',
            'meta_keywords' => 'keywords',
            'estado' => true,
            'imagenes' => [],
            'progreso' => [
                ['titulo' => 'Progreso 1', 'porcentaje' => 80, 'descripcion' => 'Desc', 'orden' => 0],
            ],
        ];
        $response = $this->putJson('/api/pagina-nosotros', $payload);
        $response->assertStatus(200);
        $response->assertJsonFragment(['titulo' => 'Quiénes somos']);
        $this->assertDatabaseHas('pagina_nosotros', ['titulo' => 'Quiénes somos']);
        $this->assertDatabaseHas('pagina_nosotros_progreso', ['titulo' => 'Progreso 1', 'porcentaje' => 80]);
    }

    /** @test */
    public function pagina_historia_show_devuelve_estructura(): void
    {
        $this->authRequest();
        $response = $this->getJson('/api/pagina-historia');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'titulo', 'estado', 'eventos', 'imagenes',
        ]);
        $this->assertIsArray($response->json('eventos'));
        $this->assertIsArray($response->json('imagenes'));
    }

    /** @test */
    public function pagina_historia_update_acepta_estructura_completa(): void
    {
        PaginaHistoria::query()->delete();
        $this->authRequest();
        $payload = [
            'titulo' => 'Nuestra historia',
            'meta_descripcion' => null,
            'meta_keywords' => null,
            'estado' => true,
            'eventos' => [
                ['anio' => 2020, 'titulo' => 'Año 2020', 'descripcion' => 'Desc', 'orden' => 0],
            ],
            'imagenes' => [],
        ];
        $response = $this->putJson('/api/pagina-historia', $payload);
        $response->assertStatus(200);
        $response->assertJsonFragment(['titulo' => 'Nuestra historia']);
        $this->assertDatabaseHas('pagina_historia', ['titulo' => 'Nuestra historia']);
        $this->assertDatabaseHas('pagina_historia_eventos', ['anio' => 2020, 'titulo' => 'Año 2020']);
    }

    /** @test */
    public function pagina_tecnologia_show_devuelve_estructura(): void
    {
        $this->authRequest();
        $response = $this->getJson('/api/pagina-tecnologia');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'titulo', 'estado', 'secciones',
        ]);
        $this->assertIsArray($response->json('secciones'));
    }

    /** @test */
    public function pagina_tecnologia_update_acepta_estructura_completa(): void
    {
        PaginaTecnologia::query()->delete();
        $this->authRequest();
        $payload = [
            'titulo' => 'Tecnología',
            'contenido' => '<p>Contenido</p>',
            'meta_descripcion' => null,
            'meta_keywords' => null,
            'estado' => true,
            'secciones' => [
                ['titulo' => 'Sección 1', 'contenido' => 'Contenido sección', 'orden' => 0],
            ],
        ];
        $response = $this->putJson('/api/pagina-tecnologia', $payload);
        $response->assertStatus(200);
        $response->assertJsonFragment(['titulo' => 'Tecnología']);
        $this->assertDatabaseHas('pagina_tecnologia', ['titulo' => 'Tecnología']);
        $this->assertDatabaseHas('pagina_tecnologia_secciones', ['titulo' => 'Sección 1']);
    }

    /** @test */
    public function pagina_aviso_show_devuelve_estructura(): void
    {
        $this->authRequest();
        $response = $this->getJson('/api/pagina-aviso');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'titulo', 'estado', 'secciones',
        ]);
        $this->assertIsArray($response->json('secciones'));
    }

    /** @test */
    public function pagina_aviso_update_acepta_estructura_completa(): void
    {
        PaginaAviso::query()->delete();
        $this->authRequest();
        $payload = [
            'titulo' => 'Aviso de Privacidad',
            'meta_descripcion' => null,
            'meta_keywords' => null,
            'estado' => true,
            'secciones' => [
                [
                    'titulo' => 'Sección 1',
                    'contenido' => 'Contenido',
                    'orden' => 0,
                    'listas' => [
                        ['texto' => 'Item 1', 'orden' => 0],
                    ],
                ],
            ],
        ];
        $response = $this->putJson('/api/pagina-aviso', $payload);
        $response->assertStatus(200);
        $response->assertJsonFragment(['titulo' => 'Aviso de Privacidad']);
        $this->assertDatabaseHas('pagina_aviso', ['titulo' => 'Aviso de Privacidad']);
        $this->assertDatabaseHas('pagina_aviso_secciones', ['titulo' => 'Sección 1']);
        $this->assertDatabaseHas('pagina_aviso_listas', ['texto' => 'Item 1']);
    }

    /** @test */
    public function modulos_requieren_autenticacion(): void
    {
        $this->getJson('/api/pagina-nosotros')->assertStatus(401);
        $this->putJson('/api/pagina-nosotros', ['titulo' => 'X', 'estado' => true])->assertStatus(401);
    }
}
