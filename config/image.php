<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Redimensionamiento opcional de imágenes
    |--------------------------------------------------------------------------
    | Si se definen, las imágenes que superen estos tamaños se redimensionan
    | antes de guardar (proporcional). Dejar en null para no redimensionar.
    */

    'max_width' => env('IMAGE_MAX_WIDTH'),   // ej. 1920 para activar redimensionamiento
    'max_height' => env('IMAGE_MAX_HEIGHT'), // ej. 1920

];
