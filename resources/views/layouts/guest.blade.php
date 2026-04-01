<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Casa Oro') }}</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,600,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-50 h-screen overflow-hidden">
    <div class="h-full flex items-center justify-center p-4">
        <div x-data="{ expanded: false }"
            class="flex flex-col sm:flex-row bg-white shadow-2xl overflow-hidden rounded-[2rem] transition-all duration-[850ms]"
            style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);"
            :class="expanded ? 'w-full max-w-5xl h-full max-h-[85vh]' : 'w-full max-w-[500px] h-[520px]'">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
