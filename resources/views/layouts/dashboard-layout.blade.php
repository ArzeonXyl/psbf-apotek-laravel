<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Mengambil judul dari komponen Livewire atau default --}}
    <title>{{ $title ?? 'Apoteker Dashboard' }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{-- Font Awesome untuk Ikon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="flex h-screen bg-gray-100 dark:bg-gray-800">
        {{-- Sidebar --}}
        {{-- Komponen Livewire yang me-render layout ini akan menyediakan $sidebarOpen --}}
        <div x-data="{ sidebarOpen: $wire.entangle('sidebarOpen') }"
             class="flex-shrink-0 w-64 bg-gray-800 text-gray-300
                    md:block transition-all duration-300 ease-in-out"
             :class="{ 'block fixed h-full z-30': sidebarOpen, 'hidden': !sidebarOpen }">
            <div class="flex items-center justify-between p-4 mb-4 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">Apotek Dashboard</h1>
                {{-- Tombol Close Sidebar untuk Mobile --}}
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="space-y-1 px-2">
                {{-- Sesuaikan nama route dengan yang Anda definisikan di web.php --}}
                <a href="{{ route('apoteker.dashboard') }}" wire:navigate
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors duration-200 text-sm font-medium
                          {{ request()->routeIs('apoteker.dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt fa-fw mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('apoteker.orders') }}" wire:navigate
                   class="flex items-center px-3 py-2.5 rounded-lg transition-colors duration-200 text-sm font-medium
                          {{ request()->routeIs('apoteker.orders') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-clipboard-list fa-fw mr-3"></i>
                    <span>Order List</span>
                </a>
                {{-- Tambahkan link lain di sini --}}
            </nav>

            {{-- Tombol Logout di paling bawah sidebar --}}
            <div class="absolute bottom-4 left-0 w-full px-4">
                {{-- Menggunakan form agar bisa logout dengan POST, tapi dipicu oleh komponen Livewire --}}
                <form wire:submit="logout" class="w-full">
                    <button type="submit"
                            class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg
                                   hover:bg-red-700 transition-colors duration-200 text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header / Navbar --}}
            <header class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border-b dark:border-gray-700">
                {{-- Tombol Hamburger untuk Mobile --}}
                <button @click="$wire.sidebarOpen = true" class="md:hidden text-gray-600 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                {{-- Menampilkan judul yang dikirim dari komponen anak --}}
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ $title ?? 'Dashboard' }}</h2>
                <div class="relative">
                    {{-- Menampilkan nama user yang login --}}
                    @auth
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                    @else
                        <span class="font-medium text-gray-700 dark:text-gray-300">Guest</span>
                    @endauth
                </div>
            </header>

            {{-- Content Slot --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                {{ $slot }} {{-- Ini adalah tempat konten spesifik halaman akan dirender --}}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>