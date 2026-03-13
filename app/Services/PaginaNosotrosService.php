<?php

namespace App\Services;

use App\Models\PaginaNosotros;
use App\Models\PaginaNosotrosImagen;
use App\Models\PaginaNosotrosProgreso;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PaginaNosotrosService
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    /**
     * Sincronizar la entidad PaginaNosotros con sus imágenes y barras de progreso.
     * Comentario: Encapsula la lógica de crear/actualizar/eliminar registros hijos.
     */
    public function updateFromRequest(PaginaNosotros $pagina, Request $request): PaginaNosotros
    {
        $data = $request->only([
            'titulo',
            'subtitulo',
            'texto_descriptivo',
            'texto_adicional',
            'url_video',
            'meta_descripcion',
            'meta_keywords',
            'estado',
        ]);

        // Imagen destacada opcional
        if ($request->hasFile('imagen_destacada')) {
            $this->imageService->deleteImage($pagina->imagen_destacada);
            $data['imagen_destacada'] = $this->imageService
                ->storeImage($request->file('imagen_destacada'), 'nosotros/destacada');
        }

        $pagina->update($data);

        $this->syncImagenes($pagina, $request);
        $this->syncProgreso($pagina, $request);

        return $pagina->load([
            'imagenes' => fn ($q) => $q->orderBy('orden'),
            'progreso' => fn ($q) => $q->orderBy('orden'),
        ]);
    }

    /**
     * Comentario: Sincroniza las imágenes de la página Nosotros con base en el arreglo recibido.
     */
    protected function syncImagenes(PaginaNosotros $pagina, Request $request): void
    {
        $items = $request->input('imagenes', []);

        $idsEnviados = collect($items)
            ->pluck('id')
            ->filter()
            ->all();

        // Eliminar imágenes que no vienen en el payload
        $pagina->imagenes()
            ->whereNotIn('id', $idsEnviados ?: [-1])
            ->get()
            ->each(function (PaginaNosotrosImagen $imagen) {
                $this->imageService->deleteImage($imagen->ruta_imagen);
                $imagen->delete();
            });

        foreach ($items as $index => $item) {
            $attributes = [
                'pagina_nosotros_id' => $pagina->id,
            ];

            if (!empty($item['id'])) {
                $imagen = PaginaNosotrosImagen::where('id', $item['id'])
                    ->where('pagina_nosotros_id', $pagina->id)
                    ->first();
            } else {
                $imagen = new PaginaNosotrosImagen($attributes);
            }

            $imagen->orden = Arr::get($item, 'orden', $index);

            if (Arr::has($item, 'ruta_imagen')) {
                $imagen->ruta_imagen = $item['ruta_imagen'];
            }

            // Archivo nuevo opcional
            if ($request->hasFile("imagenes.$index.archivo")) {
                $this->imageService->deleteImage($imagen->ruta_imagen);
                $file = $request->file("imagenes.$index.archivo");
                $imagen->ruta_imagen = $this->imageService->storeImage($file, 'nosotros/galeria');
            }

            $imagen->save();
        }
    }

    /**
     * Comentario: Sincroniza las barras de progreso de la página Nosotros.
     */
    protected function syncProgreso(PaginaNosotros $pagina, Request $request): void
    {
        $items = $request->input('progreso', []);

        $idsEnviados = collect($items)
            ->pluck('id')
            ->filter()
            ->all();

        $pagina->progreso()
            ->whereNotIn('id', $idsEnviados ?: [-1])
            ->delete();

        foreach ($items as $index => $item) {
            $attributes = [
                'pagina_nosotros_id' => $pagina->id,
            ];

            if (!empty($item['id'])) {
                $barra = PaginaNosotrosProgreso::where('id', $item['id'])
                    ->where('pagina_nosotros_id', $pagina->id)
                    ->first();
            } else {
                $barra = new PaginaNosotrosProgreso($attributes);
            }

            $barra->titulo = $item['titulo'];
            $barra->porcentaje = $item['porcentaje'];
            $barra->descripcion = $item['descripcion'] ?? null;
            $barra->orden = Arr::get($item, 'orden', $index);

            $barra->save();
        }
    }
}

