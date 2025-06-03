<div>
    {{-- Input untuk Pencarian Produk --}}
    <div class="mb-8"> {{-- Menambah margin bawah lebih besar --}}
        <input 
            wire:model.live.debounce.300ms="search"
            type="text" 
            placeholder="Cari nama obat, kategori, atau keterangan..." 
            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm 
                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                   text-sm placeholder-gray-400"
        >
    </div>

    {{-- Cek apakah ada obat untuk ditampilkan --}}
    @if($obats->count() > 0)
        {{-- Grid untuk menampilkan obat secara responsif --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-8">
            @foreach ($obats as $obat)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col group transition-all duration-300 hover:shadow-2xl">
                    {{-- Bagian Gambar Produk (Placeholder) --}}
                    <div class="h-48 w-full bg-gray-100 flex items-center justify-center overflow-hidden">
                        {{-- Ganti dengan gambar asli jika ada:
                        <img src="{{ $obat->URL_GAMBAR_ANDA ?? 'https://via.placeholder.com/400x300.png?text=Gambar+Obat' }}" 
                             alt="{{ $obat->NAMA_OBAT }}" 
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                        --}}
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>

                    {{-- Bagian Konten Card --}}
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1 truncate" title="{{ $obat->NAMA_OBAT }}">
                            {{ $obat->NAMA_OBAT }}
                        </h3>
                        
                        @if($obat->KATEGORI)
                        <p class="text-xs text-gray-500 mb-3 uppercase tracking-wider">{{ $obat->KATEGORI }}</p>
                        @endif
                        
                        {{-- Deskripsi dengan tinggi minimum agar konsisten --}}
                        <p class="text-sm text-gray-600 mb-4 flex-grow min-h-[4.5rem] line-clamp-3"> 
                            {{ $obat->KETERANGAN }}
                        </p>

                        {{-- Harga dan Tombol diletakkan di bagian bawah card --}}
                        <div class="mt-auto">
                            <div class="mb-4">
                                <span class="text-2xl font-bold text-gray-900">Rp{{ number_format($obat->HARGA, 0, ',', '.') }}</span>
                            </div>
                            
                            <a href="{{ route('toko.produk.show', ['product' => $obat->ID_OBAT]) }}" 
                               class="block w-full text-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-md 
                                      font-semibold text-sm text-white hover:bg-indigo-700 
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 
                                      transition ease-in-out duration-150">
                               Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Navigasi untuk Paginasi --}}
        <div class="mt-10">
            {{ $obats->links() }}
        </div>
    @else
        {{-- Pesan jika tidak ada produk --}}
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if(empty($search))
                    Belum Ada Obat
                @else
                    Pencarian Tidak Ditemukan
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(empty($search))
                    Saat ini belum ada obat yang tersedia di toko kami.
                @else
                    Tidak ada obat yang cocok dengan pencarian "{{ $search }}". Coba kata kunci lain.
                @endif
            </p>
        </div>
    @endif
</div>
{{-- ini list --}}