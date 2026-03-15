<?php

namespace App\Services;

use App\Models\PaginaTecnologia;
use App\Models\PaginaTecnologiaSeccion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Sincroniza pagina_tecnologia + pagina_tecnologia_secciones desde el payload del admin.
 */
class PaginaTecnologiaService
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    public function updateFromRequest(PaginaTecnologia $pagina, Request $request): PaginaTecnologia
    {
        $data = $request->only([
            'titulo',
            'contenido',
            'meta_descripcion',
            'meta_keywords',
            'estado',
        ]);

        if ($request->hasFile('imagen_destacada')) {
            $this->imageService->deleteImage($pagina->imagen_destacada);
            $data['imagen_destacada'] = $this->imageService
                ->storeImage($request->file('imagen_destacada'), 'tecnologia/destacada');
        }

        $pagina->update($data);
        $this->syncSecciones($pagina, $request);

        return $pagina->load(['secciones' => fn ($q) => $q->orderBy('orden')]);
    }

    protected function syncSecciones(PaginaTecnologia $pagina, Request $request): void
    {
        $items = $request->input('secciones', []);
        $idsEnviados = collect($items)->pluck('id')->filter()->all();

        $pagina->secciones()->whereNotIn('id', $idsEnviados ?: [-1])->delete();

        foreach ($items as $index => $item) {
            $seccion = !empty($item['id'])
                ? PaginaTecnologiaSeccion::where('id', $item['id'])->where('pagina_tecnologia_id', $pagina->id)->first()
                : new PaginaTecnologiaSeccion(['pagina_tecnologia_id' => $pagina->id]);

            $seccion->titulo = Arr::get($item, 'titulo', '');
            $seccion->contenido = Arr::get($item, 'contenido', '');
            $seccion->orden = Arr::get($item, 'orden', $index);
            $seccion->save();
        }
    }
}
