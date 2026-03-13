<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaginaNosotrosRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Comentario: La autorización se controla mediante middleware de permisos.
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'subtitulo' => ['nullable', 'string', 'max:255'],
            'texto_descriptivo' => ['nullable', 'string'],
            'texto_adicional' => ['nullable', 'string'],
            'url_video' => ['nullable', 'url', 'max:255'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'meta_descripcion' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],

            'imagenes' => ['nullable', 'array'],
            'imagenes.*.id' => ['nullable', 'integer', 'exists:pagina_nosotros_imagenes,id'],
            'imagenes.*.ruta_imagen' => ['nullable', 'string', 'max:255'],
            'imagenes.*.orden' => ['required', 'integer', 'min:0'],
            'imagenes.*.archivo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],

            'progreso' => ['nullable', 'array'],
            'progreso.*.id' => ['nullable', 'integer', 'exists:pagina_nosotros_progreso,id'],
            'progreso.*.titulo' => ['required', 'string', 'max:255'],
            'progreso.*.porcentaje' => ['required', 'integer', 'min:0', 'max:100'],
            'progreso.*.descripcion' => ['nullable', 'string'],
            'progreso.*.orden' => ['required', 'integer', 'min:0'],
        ];
    }
}

