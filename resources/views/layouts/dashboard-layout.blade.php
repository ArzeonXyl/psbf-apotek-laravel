<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Apoteker Dashboard' }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    {{-- 
        1. PERBAIKAN: x-data sekarang dikelola oleh Alpine.js saja, tidak lagi di-entangle ke Livewire.
           Ini membuat state buka-tutup sidebar murni di sisi klien dan instan.
    --}}
    <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="h-screen flex overflow-hidden bg-primary:bg-primary">
        
        {{-- 
            2. PERBAIKAN: Logika class diubah untuk menggunakan animasi slide (translate)
               dan menangani tampilan di desktop (md:relative md:translate-x-0) vs mobile (fixed).
        --}}
        <aside 
            class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-800 text-gray-300 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            
            <div class="flex items-center justify-between p-4 mb-4 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">Apotek Dashboard</h1>
                {{-- Tombol Close Sidebar untuk Mobile --}}
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- LINK NAVIGASI ANDA TIDAK SAYA UBAH --}}
            <nav class="space-y-1 px-2">
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
            </nav>

            {{-- TOMBOL LOGOUT ANDA TIDAK SAYA UBAH --}}
            <div class="absolute bottom-4 left-0 w-full px-4">
                <form wire:submit="logout" class="w-full">
                    <button type="submit"
                            class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg
                                   hover:bg-red-700 transition-colors duration-200 text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- 3. PERBAIKAN: Menambahkan div backdrop untuk overlay gelap di mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" x-transition.opacity></div>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header / Top Navbar --}}
            <header class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shadow-sm">
                {{-- 4. PERBAIKAN: Tombol hamburger sekarang hanya mengubah state Alpine.js, bukan Livewire --}}
                <button @click="sidebarOpen = true" class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ $title ?? 'Dashboard' }}</h2>
                
                <div class="relative">
                    @auth
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                    @else
                        <span class="font-medium text-gray-700 dark:text-gray-300">Guest</span>
                    @endauth
                </div>
            </header>
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>