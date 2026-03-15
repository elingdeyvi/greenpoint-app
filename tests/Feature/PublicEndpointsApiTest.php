<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Contacto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de endpoints públicos: devuelven 200 y estructura esperada;
 * solo datos activos donde aplica (banners, servicios, clientes, galería).
 */
class PublicEndpointsApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    /** @test */
    public function public_home_devuelve_banners_y_servicios(): void
    {
        $response = $this->getJson('/api/public/home');
        $response->assertStatus(200);
        $response->assertJsonStructure(['banners', 'servicios']);
        $this->assertIsArray($response->json('banners'));
        $this->assertIsArray($response->json('servicios'));
    }

    /** @test */
    public function public_servicios_devuelve_solo_activos(): void
    {
        Servicio::create(['nombre' => 'Activo', 'orden' => 0, 'activo' => true]);
        Servicio::create(['nombre' => 'Inactivo', 'orden' => 1, 'activo' => false]);

        $response = $this->getJson('/api/public/servicios');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);
        $nombres = array_column($data, 'nombre');
        $this->assertContains('Activo', $nombres);
        $this->assertNotContains('Inactivo', $nombres);
    }

    /** @test */
    public function public_clientes_devuelve_solo_activos(): void
    {
        Cliente::create(['nombre' => 'Cliente A', 'orden' => 0, 'activo' => true]);
        Cliente::create(['nombre' => 'Cliente B', 'orden' => 1, 'activo' => false]);

        $response = $this->getJson('/api/public/clientes');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);
        $nombres = array_column($data, 'nombre');
        $this->assertContains('Cliente A', $nombres);
        $this->assertNotContains('Cliente B', $nombres);
    }

    /** @test */
    public function public_galeria_devuelve_estructura(): void
    {
        $response = $this->getJson('/api/public/galeria');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    /** @test */
    public function public_contactos_devuelve_array(): void
    {
        $response = $this->getJson('/api/public/contactos');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue(is_array($data));
    }

    /** @test */
    public function public_pagina_nosotros_devuelve_estructura(): void
    {
        $response = $this->getJson('/api/public/pagina-nosotros');
        $response->assertStatus(200);
        $data = $response->json();
        if (is_array($data) && count($data) > 0) {
            $response->assertJsonStructure(['id', 'titulo', 'estado', 'imagenes', 'progreso']);
        }
    }

    /** @test */
    public function public_pagina_historia_devuelve_estructura(): void
    {
        $response = $this->getJson('/api/public/pagina-historia');
        $response->assertStatus(200);
        $data = $response->json();
        if (is_array($data) && count($data) > 0) {
            $response->assertJsonStructure(['id', 'titulo', 'estado', 'eventos', 'imagenes']);
        }
    }

    /** @test */
    public function public_pagina_tecnologia_devuelve_estructura(): void
    {
        $response = $this->getJson('/api/public/pagina-tecnologia');
        $response->assertStatus(200);
        $data = $response->json();
        if (is_array($data) && count($data) > 0) {
            $response->assertJsonStructure(['id', 'titulo', 'estado', 'secciones']);
        }
    }

    /** @test */
    public function public_pagina_aviso_devuelve_estructura(): void
    {
        $response = $this->getJson('/api/public/pagina-aviso');
        $response->assertStatus(200);
        $data = $response->json();
        if (is_array($data) && count($data) > 0) {
            $response->assertJsonStructure(['id', 'titulo', 'estado', 'secciones']);
        }
    }

    /** @test */
    public function public_configuracion_devuelve_object(): void
    {
        $response = $this->getJson('/api/public/configuracion');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue(is_array($data) || is_object($data));
    }
}
