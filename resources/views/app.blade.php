<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'GreenPoint') }}</title>

        <link rel="shortcut icon" href="/images/greenpoint/favicon.png">
        <link rel="apple-touch-icon" href="/images/greenpoint/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/images/greenpoint/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/images/greenpoint/apple-touch-icon-114x114.png">

        @php
            $component = $page['component'] ?? '';
            $isPublicSite = str_starts_with($component, 'Public/');
            $viteEntry = $isPublicSite ? 'resources/js/public-app.js' : 'resources/js/app.js';
        @endphp

        @if ($isPublicSite)
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
        @else
            <link rel="preconnect" href="https://fonts.bunny.net">
            <link href="https://fonts.bunny.net/css?family=source-sans-3:300,400,500,600,700&display=swap" rel="stylesheet" />
        @endif

        @routes
        @vite([$viteEntry, "resources/js/Pages/{$component}.vue"])
        @inertiaHead
    </head>
    <body class="{{ $isPublicSite ? 'gp-public' : '' }}">
        @inertia
    </body>
</html>
