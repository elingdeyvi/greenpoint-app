<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaginaTecnologiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'contenido' => ['nullable', 'string'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'meta_descripcion' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],

            'secciones' => ['nullable', 'array'],
            'secciones.*.id' => ['nullable', 'integer', 'exists:pagina_tecnologia_secciones,id'],
            'secciones.*.titulo' => ['required', 'string', 'max:255'],
            'secciones.*.contenido' => ['required', 'string'],
            'secciones.*.orden' => ['required', 'integer', 'min:0'],
        ];
    }
}
