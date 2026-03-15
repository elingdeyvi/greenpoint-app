<?php

namespace App\Services;

use App\Models\PaginaAviso;
use App\Models\PaginaAvisoSeccion;
use App\Models\PaginaAvisoLista;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Sincroniza pagina_aviso + pagina_aviso_secciones + pagina_aviso_listas desde el payload del admin.
 */
class PaginaAvisoService
{
    public function updateFromRequest(PaginaAviso $pagina, Request $request): PaginaAviso
    {
        $pagina->update($request->only([
            'titulo',
            'meta_descripcion',
            'meta_keywords',
            'estado',
        ]));

        $this->syncSecciones($pagina, $request);

        return $pagina->load(['secciones.listas' => fn ($q) => $q->orderBy('orden')]);
    }

    protected function syncSecciones(PaginaAviso $pagina, Request $request): void
    {
        $items = $request->input('secciones', []);
        $idsEnviados = collect($items)->pluck('id')->filter()->all();

        $pagina->secciones()->whereNotIn('id', $idsEnviados ?: [-1])->delete();

        foreach ($items as $index => $item) {
            $seccion = !empty($item['id'])
                ? PaginaAvisoSeccion::where('id', $item['id'])->where('pagina_aviso_id', $pagina->id)->first()
                : new PaginaAvisoSeccion(['pagina_aviso_id' => $pagina->id]);

            $seccion->titulo = Arr::get($item, 'titulo', '');
            $seccion->contenido = Arr::get($item, 'contenido', '');
            $seccion->orden = Arr::get($item, 'orden', $index);
            $seccion->save();

            $this->syncListas($seccion, $item['listas'] ?? []);
        }
    }

    protected function syncListas(PaginaAvisoSeccion $seccion, array $items): void
    {
        $idsEnviados = collect($items)->pluck('id')->filter()->all();

        $seccion->listas()->whereNotIn('id', $idsEnviados ?: [-1])->delete();

        foreach ($items as $idx => $item) {
            $lista = !empty($item['id'])
                ? PaginaAvisoLista::where('id', $item['id'])->where('pagina_aviso_seccion_id', $seccion->id)->first()
                : new PaginaAvisoLista(['pagina_aviso_seccion_id' => $seccion->id]);

            $lista->texto = Arr::get($item, 'texto', '');
            $lista->orden = Arr::get($item, 'orden', $idx);
            $lista->save();
        }
    }
}
