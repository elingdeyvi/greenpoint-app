<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePaginaHomeRequest;
use App\Models\Configuracion;
use App\Services\PublicSiteService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaginaHomeController extends Controller
{
    private const SCALAR_KEYS = [
        'home_servicios_titulo',
        'home_servicios_subtitulo',
        'home_cta_titulo',
        'home_cta_texto',
        'home_video_url',
        'anos_experiencia',
    ];

    private const JSON_KEYS = [
        'home_service_cards',
        'home_about_checks',
        'home_why_left',
        'home_why_right',
        'home_feature_cards',
    ];

    public function __construct(private readonly PublicSiteService $publicSiteService)
    {
    }

    public function edit(): Response
    {
        $keys = [...self::SCALAR_KEYS, ...self::JSON_KEYS];

        $config = Configuracion::query()
            ->whereIn('clave', $keys)
            ->pluck('valor', 'clave');

        return Inertia::render('Admin/Paginas/Home', [
            'home' => [
                'home_servicios_titulo' => (string) ($config['home_servicios_titulo'] ?? ''),
                'home_servicios_subtitulo' => (string) ($config['home_servicios_subtitulo'] ?? ''),
                'home_cta_titulo' => (string) ($config['home_cta_titulo'] ?? ''),
                'home_cta_texto' => (string) ($config['home_cta_texto'] ?? ''),
                'home_video_url' => (string) ($config['home_video_url'] ?? ''),
                'anos_experiencia' => (string) ($config['anos_experiencia'] ?? ''),
                'home_service_cards' => $this->decodeJsonArray($config['home_service_cards'] ?? null),
                'home_about_checks' => $this->decodeJsonArray($config['home_about_checks'] ?? null),
                'home_why_left' => $this->decodeJsonArray($config['home_why_left'] ?? null),
                'home_why_right' => $this->decodeJsonArray($config['home_why_right'] ?? null),
                'home_feature_cards' => $this->decodeJsonArray($config['home_feature_cards'] ?? null),
            ],
        ]);
    }

    public function update(UpdatePaginaHomeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        foreach (self::SCALAR_KEYS as $clave) {
            Configuracion::query()->updateOrCreate(
                ['clave' => $clave],
                ['valor' => $data[$clave] ?? ''],
            );
        }

        foreach (self::JSON_KEYS as $clave) {
            $value = $data[$clave] ?? [];
            Configuracion::query()->updateOrCreate(
                ['clave' => $clave],
                ['valor' => json_encode(array_values($value), JSON_UNESCAPED_UNICODE)],
            );
        }

        $this->publicSiteService->forgetCache();

        return redirect()
            ->back()
            ->with('success', 'Página Inicio actualizada correctamente.');
    }

    /**
     * @return array<int, mixed>
     */
    private function decodeJsonArray(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return array_values($raw);
        }

        try {
            $parsed = json_decode((string) $raw, true, 512, JSON_THROW_ON_ERROR);

            return is_array($parsed) ? array_values($parsed) : [];
        } catch (\JsonException) {
            return [];
        }
    }
}
