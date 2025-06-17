{{-- resources/views/layouts/layout-toko.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/toko.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="relative min-h-screen overflow-hidden">
        {{-- Navbar UTAMA (Hanya di sini) --}}
        <nav class="relative z-10 flex items-center justify-between px-8 py-4 bg-white shadow-md">
            <div class="flex items-center space-x-2">
                <i class="fas fa-prescription-bottle-alt text-2xl text-primary-dark"></i>
                <a href="{{ route('toko.landing') }}" class="text-2xl font-bold text-gray-800">Apotek Sehat</a>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('toko.landing') }}" class="text-gray-600 hover:text-primary-dark font-medium">Home</a>
                <a href="#" class="text-gray-600 hover:text-primary-dark font-medium">Obat</a>
                <a href="#" class="text-gray-600 hover:text-primary-dark font-medium">Tentang Kami</a>
            </div>
        </nav>

        <main>
            {{ $slot }} {{-- Ini adalah tempat konten dari komponen Livewire akan dirender --}}
        </main>

        {{-- Anda bisa tambahkan footer di sini jika ada --}}
    </div>
    @livewireScripts
</body>
</html>