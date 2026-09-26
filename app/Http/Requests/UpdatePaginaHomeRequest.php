<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaginaHomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'home_servicios_titulo' => ['nullable', 'string', 'max:255'],
            'home_servicios_subtitulo' => ['nullable', 'string', 'max:255'],
            'home_cta_titulo' => ['nullable', 'string', 'max:255'],
            'home_cta_texto' => ['nullable', 'string'],
            'home_video_url' => ['nullable', 'string', 'max:500'],
            'anos_experiencia' => ['nullable', 'string', 'max:20'],

            'home_service_cards' => ['nullable', 'array'],
            'home_service_cards.*.nombre' => ['required', 'string', 'max:255'],
            'home_service_cards.*.descripcion' => ['nullable', 'string'],
            'home_service_cards.*.icon' => ['nullable', 'string', 'max:500'],

            'home_about_checks' => ['nullable', 'array'],
            'home_about_checks.*' => ['required', 'string', 'max:255'],

            'home_why_left' => ['nullable', 'array'],
            'home_why_left.*.title' => ['required', 'string', 'max:255'],
            'home_why_left.*.text' => ['nullable', 'string'],
            'home_why_left.*.icon' => ['nullable', 'string', 'max:500'],

            'home_why_right' => ['nullable', 'array'],
            'home_why_right.*.title' => ['required', 'string', 'max:255'],
            'home_why_right.*.text' => ['nullable', 'string'],
            'home_why_right.*.icon' => ['nullable', 'string', 'max:500'],

            'home_feature_cards' => ['nullable', 'array'],
            'home_feature_cards.*.title' => ['required', 'string', 'max:255'],
            'home_feature_cards.*.icon' => ['nullable', 'string', 'max:255'],
        ];
    }
}
