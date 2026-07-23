<?php

namespace App\Services;

use App\Models\PaginaAviso;
use App\Models\PaginaAvisoLista;
use App\Models\PaginaAvisoSeccion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PaginaAvisoService
{
    /**
     * Sincronizar la página Aviso con secciones y listas.
     */
    public function updateFromRequest(PaginaAviso $pagina, Request $request): PaginaAviso
    {
        $data = $request->only([
            'titulo',
            'meta_descripcion',
            'meta_keywords',
            'estado',
        ]);

        $pagina->update($data);

        $this->syncSecciones($pagina, $request);

        return $pagina->load([
            'secciones.listas' => fn ($q) => $q->orderBy('orden'),
        ]);
    }

    protected function syncSecciones(PaginaAviso $pagina, Request $request): void
    {
        $items = $request->input('secciones', []);

        $idsEnviados = collect($items)
            ->pluck('id')
            ->filter()
            ->all();

        $seccionesAEliminar = $pagina->secciones()
            ->whereNotIn('id', $idsEnviados ?: [-1])
            ->get();

        foreach ($seccionesAEliminar as $seccion) {
            $seccion->listas()->delete();
            $seccion->delete();
        }

        foreach ($items as $index => $item) {
            $attributes = [
                'pagina_aviso_id' => $pagina->id,
            ];

            if (!empty($item['id'])) {
                $seccion = PaginaAvisoSeccion::where('id', $item['id'])
                    ->where('pagina_aviso_id', $pagina->id)
                    ->first();
            } else {
                $seccion = new PaginaAvisoSeccion($attributes);
            }

            $seccion->titulo = $item['titulo'];
            $seccion->contenido = $item['contenido'] ?? null;
            $seccion->orden = Arr::get($item, 'orden', $index);
            $seccion->save();

            $this->syncListas($seccion, $item['listas'] ?? []);
        }
    }

    protected function syncListas(PaginaAvisoSeccion $seccion, array $items): void
    {
        $idsEnviados = collect($items)
            ->pluck('id')
            ->filter()
            ->all();

        $seccion->listas()
            ->whereNotIn('id', $idsEnviados ?: [-1])
            ->delete();

        foreach ($items as $index => $item) {
            $attributes = [
                'pagina_aviso_seccion_id' => $seccion->id,
            ];

            if (!empty($item['id'])) {
                $lista = PaginaAvisoLista::where('id', $item['id'])
                    ->where('pagina_aviso_seccion_id', $seccion->id)
                    ->first();
            } else {
                $lista = new PaginaAvisoLista($attributes);
            }

            $lista->texto = $item['texto'];
            $lista->orden = Arr::get($item, 'orden', $index);
            $lista->save();
        }
    }
}
