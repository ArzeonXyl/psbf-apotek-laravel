<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Font Awesome CDN sudah dikomentari, pastikan sudah diimport via NPM/app.css jika diperlukan --}}
        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0ot92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR3+tf/F6Y5B5A==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}

        {{-- Ini adalah tempat untuk @vite dan @livewireStyles --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles {{-- PASTIKAN INI ADA UNTUK CSS LIVEWIRE --}}
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{-- Kontainer ini akan memusatkan blok dua kolom di tengah halaman --}}
        <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </div>

        {{-- INI SANGAT PENTING: @livewireScripts HARUS ADA DI SINI UNTUK JAVASCRIPT LIVEWIRE/ALPINE --}}
        @livewireScripts
    </body>
</html>