<?php

namespace App\Services;

use App\Models\PaginaHistoria;
use App\Models\PaginaHistoriaEvento;
use App\Models\PaginaHistoriaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Sincroniza pagina_historia + pagina_historia_eventos + pagina_historia_imagenes desde el payload del admin.
 */
class PaginaHistoriaService
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    public function updateFromRequest(PaginaHistoria $pagina, Request $request): PaginaHistoria
    {
        $pagina->update($request->only([
            'titulo',
            'meta_descripcion',
            'meta_keywords',
            'estado',
        ]));

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
        $idsEnviados = collect($items)->pluck('id')->filter()->all();

        $pagina->eventos()->whereNotIn('id', $idsEnviados ?: [-1])->delete();

        foreach ($items as $index => $item) {
            $evento = !empty($item['id'])
                ? PaginaHistoriaEvento::where('id', $item['id'])->where('pagina_historia_id', $pagina->id)->first()
                : new PaginaHistoriaEvento(['pagina_historia_id' => $pagina->id]);

            $evento->anio = (int) Arr::get($item, 'anio', 0);
            $evento->titulo = Arr::get($item, 'titulo', '');
            $evento->descripcion = Arr::get($item, 'descripcion');
            $evento->orden = Arr::get($item, 'orden', $index);
            $evento->save();
        }
    }

    protected function syncImagenes(PaginaHistoria $pagina, Request $request): void
    {
        $items = $request->input('imagenes', []);
        $idsEnviados = collect($items)->pluck('id')->filter()->all();

        $pagina->imagenes()
            ->whereNotIn('id', $idsEnviados ?: [-1])
            ->get()
            ->each(function (PaginaHistoriaImagen $imagen) {
                $this->imageService->deleteImage($imagen->ruta_imagen);
                $imagen->delete();
            });

        foreach ($items as $index => $item) {
            $imagen = !empty($item['id'])
                ? PaginaHistoriaImagen::where('id', $item['id'])->where('pagina_historia_id', $pagina->id)->first()
                : new PaginaHistoriaImagen(['pagina_historia_id' => $pagina->id]);

            $imagen->orden = Arr::get($item, 'orden', $index);
            if (Arr::has($item, 'ruta_imagen')) {
                $imagen->ruta_imagen = $item['ruta_imagen'];
            }
            if ($request->hasFile("imagenes.$index.archivo")) {
                $this->imageService->deleteImage($imagen->ruta_imagen);
                $imagen->ruta_imagen = $this->imageService->storeImage(
                    $request->file("imagenes.$index.archivo"),
                    'historia'
                );
            }
            $imagen->save();
        }
    }
}
