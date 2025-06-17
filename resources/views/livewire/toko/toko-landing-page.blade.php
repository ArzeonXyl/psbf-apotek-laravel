{{-- resources/views/livewire/toko/toko-landing-page.blade.php --}}

<div>
    {{-- Hero Section with Gradient Background and Wave --}}
    <div class="relative bg-gradient-to-br from-primary-light to-primary-dark text-white pt-16 pb-32 md:pb-48">
        {{-- Hapus div wave-background yang lama, ganti dengan SVG ini --}}
        

        <div class="relative z-10 max-w-7xl px-8 flex items-center justify-between mx-auto">
            <div class="flex-grow min-w-0 mr-[200px] text-left">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 mt-20">
                    <span class="whitespace-nowrap">Solusi Kesehatan</span> <br>
                    <span class="text-accent whitespace-nowrap">Anda di Apotek Sehat</span>
                </h1>
                <p class="text-lg opacity-90 leading-relaxed mb-8">
                    Temukan berbagai macam obat-obatan, vitamin, dan produk kesehatan berkualitas tinggi untuk kebutuhan Anda dan keluarga. Layanan cepat, terpercaya, dan mudah dijangkau.
                </p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('login') }}"
                    class="btn-secondary py-4 px-20 rounded-full font-semibold text-xl shadow-lg transform transition duration-300 hover:scale-105">
                        <i class=""></i>Login
                    </a>
                </div>
            </div>

            <div class="flex-none">
                <lottie-player
                    src="{{ asset('storage/img/Logo.json') }}"
                    background="transparent"
                    speed="1"
                    loop
                    autoplay
                    style="width: 400px; height: 400px;"
                ></lottie-player>
            </div>
        </div>

    </div>

    {{-- Anda bisa menambahkan lebih banyak bagian konten di sini, seperti fitur, testimoni, dll. --}}
    <div class="bg-white py-20">
        <div class="max-w-6xl mx-auto px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Mengapa Memilih Apotek Sehat?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <i class="fas fa-check-circle text-4xl text-primary-dark mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Produk Berkualitas</h3>
                    <p class="text-gray-600">Kami hanya menyediakan obat-obatan dan produk kesehatan dari distributor terpercaya.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <i class="fas fa-truck text-4xl text-primary-dark mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Pengiriman Cepat</h3>
                    <p class="text-gray-600">Pesanan Anda akan tiba dengan cepat dan aman di tujuan.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <i class="fas fa-comments text-4xl text-primary-dark mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Layanan Pelanggan Ramah</h3>
                    <p class="text-gray-600">Tim kami siap membantu Anda kapan saja untuk setiap pertanyaan.</p>
                </div>
            </div>
        </div>
    </div>

</div> {{-- Penutup div root element --}}