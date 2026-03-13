<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Guardar imagen en el disco configurado y devolver la ruta relativa.
     * Comentario: Este método centraliza la lógica de almacenamiento de imágenes.
     */
    public function storeImage(UploadedFile $file, string $directory): string
    {
        // Comentario: Se guarda en el disco "public" para poder servir las imágenes fácilmente.
        return $file->store($directory, ['disk' => 'public']);
    }

    /**
     * Eliminar una imagen existente si la ruta no es nula.
     * Comentario: Se usa al reemplazar o eliminar registros con imágenes asociadas.
     */
    public function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

