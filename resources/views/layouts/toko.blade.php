<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Apotek PSBF') }} - Toko</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles {{-- Penting untuk gaya Livewire --}}
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900"> {{-- Menambahkan dark mode class jika perlu --}}
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('toko.home') }}">
                                {{-- Ganti dengan logo jika ada --}}
                                <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ config('app.name', 'Apotek PSBF') }}</h1>
                            </a>
                        </div>
                        {{-- Nav Links Toko (Contoh) --}}
                        {{-- <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('toko.home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Beranda
                            </a>
                        </div> --}}
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-400 underline">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-400 underline">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-400 underline">Register</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        @hasSection('header') {{-- Menggunakan @hasSection untuk opsional header --}}
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header') {{-- Tempat untuk header dari section --}}
                </div>
            </header>
        @endif

        <main>
            @yield('content') {{-- Tempat untuk konten utama dari section --}}
        </main>
    </div>
    @livewireScripts {{-- Penting untuk JavaScript Livewire --}}
</body>
</html>