<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaginaHistoriaRequest extends FormRequest
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

            'eventos' => ['nullable', 'array'],
            'eventos.*.id' => ['nullable', 'integer', 'exists:pagina_historia_eventos,id'],
            'eventos.*.anio' => ['required', 'integer', 'min:1900', 'max:2100'],
            'eventos.*.titulo' => ['required', 'string', 'max:255'],
            'eventos.*.descripcion' => ['nullable', 'string'],
            'eventos.*.orden' => ['required', 'integer', 'min:0'],

            'imagenes' => ['nullable', 'array'],
            'imagenes.*.id' => ['nullable', 'integer', 'exists:pagina_historia_imagenes,id'],
            'imagenes.*.ruta_imagen' => ['nullable', 'string', 'max:255'],
            'imagenes.*.orden' => ['required', 'integer', 'min:0'],
            'imagenes.*.archivo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ];
    }
}
