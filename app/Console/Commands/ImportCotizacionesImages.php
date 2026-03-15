<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportCotizacionesImages extends Command
{
    /**
     * Nombre y firma del comando.
     * Copia imágenes desde el proyecto cotizaciones al storage público de Laravel.
     */
    protected $signature = 'app:import-cotizaciones-images
                            {--path= : Ruta al proyecto cotizaciones (sobreescribe COTIZACIONES_PATH)}';

    protected $description = 'Copia imágenes desde cotizaciones (banners, clientes, galería, iconos) al storage público';

    public function handle(): int
    {
        $origen = $this->getOrigenPath();
        if (!$origen || !File::isDirectory($origen)) {
            $this->error('No se encontró el directorio de cotizaciones. Configure COTIZACIONES_PATH en .env o use --path=');
            return self::FAILURE;
        }

        $this->info("Origen: {$origen}");

        // Asegurar enlace simbólico storage -> public/storage
        if (!File::exists(public_path('storage'))) {
            $this->call('storage:link');
        }

        $copiados = 0;
        $mapeos = [
            'img/banner' => 'banners',
            'img/clientes' => 'clientes',
            'galeria' => 'galeria',
            'img/icons' => 'icons',
        ];

        foreach ($mapeos as $subcarpetaOrigen => $subcarpetaDestino) {
            $rutaOrigen = $origen . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subcarpetaOrigen);
            if (!File::isDirectory($rutaOrigen)) {
                $this->warn("No existe: {$rutaOrigen}");
                continue;
            }

            $archivos = File::files($rutaOrigen);
            foreach ($archivos as $archivo) {
                if (!$archivo->isFile()) {
                    continue;
                }
                $nombre = $archivo->getFilename();
                $destino = $subcarpetaDestino . '/' . $nombre;
                $contenido = File::get($archivo->getPathname());
                Storage::disk('public')->put($destino, $contenido);
                $this->line("  {$subcarpetaOrigen}/{$nombre} → {$destino}");
                $copiados++;
            }
        }

        $this->info("Se copiaron {$copiados} archivos.");
        return self::SUCCESS;
    }

    private function getOrigenPath(): ?string
    {
        $path = $this->option('path') ?? env('COTIZACIONES_PATH');
        if ($path === null || $path === '') {
            return base_path('../cotizaciones');
        }
        $isAbsolute = str_starts_with($path, '/') || (strlen($path) >= 2 && $path[1] === ':');
        return $isAbsolute ? $path : base_path($path);
    }
}
