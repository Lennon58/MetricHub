<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- === ALTERAÇÃO AQUI === --}}
        <!-- Favicon -->
        <!-- Se o seu arquivo for favicon.ico use x-icon. Se for favicon.png use image/png -->
        <!-- O '?v=2' força o navegador a quebrar o cache do ícone antigo -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
        {{-- ======================== --}}

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        {{-- ... restante do head ... --}}
        
        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        @inertia
    </body>
</html>