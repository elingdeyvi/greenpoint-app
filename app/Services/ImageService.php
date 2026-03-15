<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Servicio centralizado de imágenes para el sitio GreenPoint.
 *
 * Uso:
 * - Banners, Galería, Clientes (logo), Servicios: subida en store/update y eliminación en update (reemplazo) y destroy.
 * - Módulos administrables (Nosotros, Historia, Tecnología): imagen destacada e imágenes múltiples vía servicios (PaginaNosotrosService, etc.).
 *
 * Almacenamiento: disco "public" (storage/app/public). Enlace simbólico: php artisan storage:link → public/storage.
 *
 * URLs: El API devuelve la ruta relativa (ej. "banners/abc.jpg", "galeria/xyz.png"). El frontend debe construir
 * la URL pública como: baseUrl + '/storage/' + path, donde baseUrl es APP_URL o VITE_API_URL sin /api.
 */
class ImageService
{
    private const DISK = 'public';

    /**
     * Guardar imagen en el disco configurado y devolver la ruta relativa.
     * Si está configurado redimensionamiento (config/image.php), se redimensiona al superar max_width/max_height.
     */
    public function storeImage(UploadedFile $file, string $directory): string
    {
        $maxWidth = config('image.max_width');
        $maxHeight = config('image.max_height');

        if (($maxWidth || $maxHeight) && $this->isResizable($file)) {
            $path = $this->storeResized($file, $directory, (int) $maxWidth ?: 0, (int) $maxHeight ?: 0);
            if ($path !== null) {
                return $path;
            }
        }

        return $file->store($directory, ['disk' => self::DISK]);
    }

    /**
     * Eliminar una imagen existente si la ruta no es nula ni vacía.
     * Se usa al reemplazar imagen (update) o al eliminar el registro (destroy).
     */
    public function deleteImage(?string $path): void
    {
        $path = $path ? trim($path) : null;
        if ($path === null || $path === '') {
            return;
        }
        if (Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    /**
     * Obtener la URL pública de una ruta relativa (para uso opcional en API o respuestas).
     * El frontend actual construye la URL con baseUrl + '/storage/' + path.
     */
    public function urlFor(?string $path): string
    {
        if ($path === null || trim($path) === '') {
            return '';
        }
        return Storage::disk(self::DISK)->url(trim($path));
    }

    private function isResizable(UploadedFile $file): bool
    {
        $mime = $file->getMimeType();
        return in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true);
    }

    /**
     * Redimensiona la imagen si supera maxWidth/maxHeight y la guarda. Devuelve ruta o null si falla.
     */
    private function storeResized(UploadedFile $file, string $directory, int $maxWidth, int $maxHeight): ?string
    {
        $path = $file->getRealPath();
        $info = @getimagesize($path);
        if ($info === false) {
            return null;
        }

        [$width, $height] = [$info[0], $info[1]];
        $maxWidth = $maxWidth > 0 ? $maxWidth : $width;
        $maxHeight = $maxHeight > 0 ? $maxHeight : $height;

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return null;
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height, 1.0);
        $newWidth = (int) round($width * $ratio);
        $newHeight = (int) round($height * $ratio);

        $resource = $this->createImageResource($info[2], $path);
        if ($resource === null) {
            return null;
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        if ($resized === false) {
            $this->destroyResource($info[2], $resource);
            return null;
        }

        // Preservar transparencia en PNG
        if ($info[2] === IMAGETYPE_PNG) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($resized, $resource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $this->destroyResource($info[2], $resource);

        $extension = $file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION) ?: 'jpg';
        $filename = uniqid('', true).'.'.strtolower($extension);
        $relativePath = $directory.'/'.$filename;

        $content = null;
        switch (strtolower($extension)) {
            case 'png':
                ob_start();
                imagepng($resized, null, 8);
                $content = ob_get_clean();
                break;
            case 'gif':
                ob_start();
                imagegif($resized, null);
                $content = ob_get_clean();
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    ob_start();
                    imagewebp($resized, null, 85);
                    $content = ob_get_clean();
                }
                break;
            default:
                ob_start();
                imagejpeg($resized, null, 90);
                $content = ob_get_clean();
                break;
        }
        imagedestroy($resized);

        if ($content === false || $content === null || $content === '') {
            return null;
        }
        return Storage::disk(self::DISK)->put($relativePath, $content) ? $relativePath : null;
    }

    /** @return \GdImage|resource|null */
    private function createImageResource(int $type, string $path)
    {
        return match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => null,
        };
    }

    /** @param \GdImage|resource $resource */
    private function destroyResource(int $type, $resource): void
    {
        if (is_resource($resource)) {
            imagedestroy($resource);
        }
    }
}
