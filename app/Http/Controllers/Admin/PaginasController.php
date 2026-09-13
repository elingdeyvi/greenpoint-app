<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaginaAviso;
use App\Models\PaginaHistoria;
use App\Models\PaginaNosotros;
use App\Models\PaginaTecnologia;
use Inertia\Inertia;
use Inertia\Response;

class PaginasController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Paginas/Index', [
            'pages' => [
                [
                    'key' => 'nosotros',
                    'titulo' => 'Nosotros',
                    'descripcion' => 'Quiénes somos, textos e indicadores.',
                    'route' => 'admin.paginas.nosotros.edit',
                    'permission' => 'modulos.nosotros',
                    'estado' => (bool) PaginaNosotros::query()->value('estado'),
                ],
                [
                    'key' => 'historia',
                    'titulo' => 'Historia',
                    'descripcion' => 'Línea de tiempo e imágenes históricas.',
                    'route' => 'admin.paginas.historia.edit',
                    'permission' => 'modulos.historia',
                    'estado' => (bool) PaginaHistoria::query()->value('estado'),
                ],
                [
                    'key' => 'tecnologia',
                    'titulo' => 'Tecnología',
                    'descripcion' => 'Contenido e infraestructura tecnológica.',
                    'route' => 'admin.paginas.tecnologia.edit',
                    'permission' => 'modulos.tecnologia',
                    'estado' => (bool) PaginaTecnologia::query()->value('estado'),
                ],
                [
                    'key' => 'aviso',
                    'titulo' => 'Aviso de privacidad',
                    'descripcion' => 'Secciones y listas del aviso legal.',
                    'route' => 'admin.paginas.aviso.edit',
                    'permission' => 'modulos.aviso',
                    'estado' => (bool) PaginaAviso::query()->value('estado'),
                ],
            ],
        ]);
    }
}
