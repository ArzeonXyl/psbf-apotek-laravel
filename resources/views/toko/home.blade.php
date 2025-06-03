@extends('layouts.toko')  {{-- Menggunakan layout utama Toko --}}

{{-- Bagian header halaman, akan muncul di slot 'header' pada layout --}}
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Selamat Datang di Toko Apotek Kami') }}
    </h2>
@endsection

{{-- Bagian konten utama halaman, akan muncul di slot 'content' pada layout --}}
{{-- atau jika layout Anda menggunakan {{ $slot }}, maka ini akan menjadi $slot --}}
@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">{{ __("Silakan lihat produk-produk yang tersedia di apotek kami.") }}</p>
                    
                    {{-- Di sinilah kita memanggil komponen Livewire ProductList --}}
                    {{-- Komponen ini yang akan menampilkan daftar produk (dummy atau asli) --}}
                    @livewire('toko.product-list')
                </div>
            </div>
        </div>
    </div>
@endsection