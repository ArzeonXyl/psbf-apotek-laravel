{{-- resources/views/livewire/toko/obat-index.blade.php --}}

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Daftar Obat Tersedia</h2>

    {{-- Hapus bagian session success/error karena tidak ada lagi interaksi keranjang --}}
    {{-- @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse ($allObat as $obatItem)
            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col"> {{-- Hapus justify-between --}}
                <div>
                    @if ($obatItem->gambar)
                        <img src="{{ asset('storage/' . $obatItem->gambar) }}" alt="{{ $obatItem->NAMA_OBAT }}" class="w-full h-48 object-cover">
                    @else
                        <img src="{{ asset('images/default-obat.png') }}" alt="Default Obat" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $obatItem->NAMA_OBAT }}</h3>
                        <p class="text-gray-600 text-sm mb-1">Kategori: {{ $obatItem->KATEGORI }}</p>
                        <p class="text-gray-600 text-sm mb-1">Stok: {{ $obatItem->JUMLAH_STOCK }}</p>
                        <p class="text-gray-900 font-bold text-lg">Rp {{ number_format($obatItem->HARGA, 0, ',', '.') }}</p>
                        {{-- Opsional: Tampilkan keterangan jika ada --}}
                        {{-- <p class="text-gray-500 text-sm mt-2">{{ Str::limit($obatItem->KETERANGAN, 70) }}</p> --}}
                    </div>
                </div>
                {{-- HAPUS BLOK DIV INI (untuk kuantitas dan tombol keranjang)
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center space-x-2 mb-4">
                        <label for="quantity-{{ $obatItem->ID_OBAT }}" class="sr-only">Kuantitas</label>
                        <input
                            type="number"
                            id="quantity-{{ $obatItem->ID_OBAT }}"
                            wire:model.live="quantities.{{ $obatItem->ID_OBAT }}"
                            min="1"
                            max="{{ $obatItem->JUMLAH_STOCK }}"
                            class="w-20 border border-gray-300 rounded-md px-3 py-2 text-center text-gray-700 focus:ring-primary focus:border-primary"
                        >
                    </div>
                    <button
                        wire:click="addToCart({{ $obatItem->ID_OBAT }})"
                        class="btn-primary w-full py-2 px-4 rounded-md text-white font-semibold transition duration-200 ease-in-out hover:opacity-90"
                        @if ($obatItem->JUMLAH_STOCK <= 0) disabled @endif
                    >
                        @if ($obatItem->JUMLAH_STOCK <= 0)
                            Stok Habis
                        @else
                            <i class="fas fa-cart-plus mr-2"></i> Tambah ke Keranjang
                        @endif
                    </button>
                </div>
                --}}
            </div>
        @empty
            <p class="col-span-full text-center text-gray-600">Belum ada obat yang tersedia.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $allObat->links() }}
    </div>

    {{-- HAPUS BLOK LINK KERANJANG INI
    <div class="text-center mt-8">
        <a href="{{ route('toko.cart') }}" class="btn-secondary inline-flex items-center py-3 px-8 rounded-full font-bold text-lg shadow-lg transform transition duration-300 hover:scale-105">
            <i class="fas fa-shopping-cart mr-2"></i> Lihat Keranjang ({{ $cart->sum('quantity') ?? 0 }})
        </a>
    </div>
    --}}
</div>