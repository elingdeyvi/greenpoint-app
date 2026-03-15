<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaginaAvisoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'meta_descripcion' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],

            'secciones' => ['nullable', 'array'],
            'secciones.*.id' => ['nullable', 'integer', 'exists:pagina_aviso_secciones,id'],
            'secciones.*.titulo' => ['required', 'string', 'max:255'],
            'secciones.*.contenido' => ['required', 'string'],
            'secciones.*.orden' => ['required', 'integer', 'min:0'],
            'secciones.*.listas' => ['nullable', 'array'],
            'secciones.*.listas.*.id' => ['nullable', 'integer', 'exists:pagina_aviso_listas,id'],
            'secciones.*.listas.*.texto' => ['required', 'string', 'max:500'],
            'secciones.*.listas.*.orden' => ['required', 'integer', 'min:0'],
        ];
    }
}
