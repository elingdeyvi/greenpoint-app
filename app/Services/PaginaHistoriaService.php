<?php

namespace App\Services;

use App\Models\PaginaHistoria;
use App\Models\PaginaHistoriaEvento;
use App\Models\PaginaHistoriaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PaginaHistoriaService
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    /**
     * Sincronizar la página Historia con eventos e imágenes.
     */
    public function updateFromRequest(PaginaHistoria $pagina, Request $request): PaginaHistoria
    {
        $data = $request->only([
            'titulo',
            'meta_descripcion',
            'meta_keywords',
            'estado',
        ]);

        $pagina->update($data);

        $this->syncEventos($pagina, $request);
        $this->syncImagenes($pagina, $request);

        return $pagina->load([
            'eventos' => fn ($q) => $q->orderBy('orden'),
            'imagenes' => fn ($q) => $q->orderBy('orden'),
        ]);
    }

    protected function syncEventos(PaginaHistoria $pagina, Request $request): void
    {
        $items = $request->input('eventos', []);

        $idsEnviados = collect($items)
            ->pluck('id')
            ->filter()
            ->all();

        $pagina->eventos()
            ->whereNotIn('id', $idsEnviados ?: [-1])
            ->delete();

        foreach ($items as $index => $item) {
            $attributes = [
                'pagina_historia_id' => $pagina->id,
            ];

            if (!empty($item['id'])) {
                $evento = PaginaHistoriaEvento::where('id', $item['id'])
                    ->where('pagina_historia_id', $pagina->id)
                    ->first();
            } else {
                $evento = new PaginaHistoriaEvento($attributes);
            }

            $evento->anio = $item['anio'];
            $evento->titulo = $item['titulo'];
            $evento->descripcion = $item['descripcion'] ?? null;
            $evento->orden = Arr::get($item, 'orden', $index);

            $evento->save();
        }
    }

    protected function syncImagenes(PaginaHistoria $pagina, Request $request): void
    {
        $items = $request->input('imagenes', []);

        $idsEnviados = collect($items)
            ->pluck('id')
            ->filter()
            ->all();

        $pagina->imagenes()
            ->whereNotIn('id', $idsEnviados ?: [-1])
            ->get()
            ->each(function (PaginaHistoriaImagen $imagen) {
                $this->imageService->deleteImage($imagen->ruta_imagen);
                $imagen->delete();
            });

        foreach ($items as $index => $item) {
            $attributes = [
                'pagina_historia_id' => $pagina->id,
            ];

            if (!empty($item['id'])) {
                $imagen = PaginaHistoriaImagen::where('id', $item['id'])
                    ->where('pagina_historia_id', $pagina->id)
                    ->first();
            } else {
                $imagen = new PaginaHistoriaImagen($attributes);
            }

            $imagen->orden = Arr::get($item, 'orden', $index);

            if (Arr::has($item, 'ruta_imagen')) {
                $imagen->ruta_imagen = $item['ruta_imagen'];
            }

            if ($request->hasFile("imagenes.$index.archivo")) {
                $this->imageService->deleteImage($imagen->ruta_imagen);
                $file = $request->file("imagenes.$index.archivo");
                $imagen->ruta_imagen = $this->imageService->storeImage($file, 'historia/galeria');
            }

            $imagen->save();
        }
    }
}
