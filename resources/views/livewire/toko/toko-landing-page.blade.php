{{-- resources/views/livewire/toko/toko-landing-page.blade.php --}}

<div>
    {{-- Hero Section with Gradient Background and Wave --}}
    <div class="relative bg-gradient-to-br from-primary-light to-primary-dark text-white pt-16 pb-32 md:pb-48">
        {{-- Hapus div wave-background yang lama, ganti dengan SVG ini --}}
        <div class="wave-svg-container absolute bottom-0 left-0 w-full z-0 overflow-hidden">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,174.4-41.21,60.22-11.09,128.63-16.55,202.5-16.55,148.84,0,269.8,50.5,304,92.56,36.2,44.75,163.63,115.44,197.66,115.44,0,0,33.15,0,33.15,0l-.13,120H0V.89Z" class="shape-fill"></path>
            </svg>
        </div>

        {{-- NEW: Contoh Lingkaran Terpotong (Half Circle) --}}
        {{-- Tambahkan div ini untuk setengah lingkaran di pojok kanan atas --}}
        <div class="absolute top-10 right-10 z-10 circle-half-top-right">
            <div class="circle-inner"></div>
        </div>
        {{-- Tambahkan div ini untuk setengah lingkaran di pojok kiri bawah (mirrored) --}}
        <div class="absolute bottom-10 left-10 z-10 circle-half-bottom-left">
            <div class="circle-inner"></div>
        </div>
        {{-- END NEW --}}

        <div class="relative z-10 max-w-4xl mx-auto px-8 flex flex-col items-center text-center justify-center gap-12">
            <div class="pt-12 md:pt-0">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    Solusi Kesehatan Anda di <span class="text-accent">Apotek Sehat</span>
                </h1>
                <p class="text-lg md:text-xl opacity-90 leading-relaxed mb-8">
                    Temukan berbagai macam obat-obatan, vitamin, dan produk kesehatan berkualitas tinggi untuk kebutuhan Anda dan keluarga. Layanan cepat, terpercaya, dan mudah dijangkau.
                </p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center">
                    <a href="#" class="btn-secondary py-3 px-8 rounded-full font-bold text-lg shadow-lg transform transition duration-300 hover:scale-105">
                        <i class="fas fa-pills mr-2"></i>Lihat Obat
                    </a>
                    <a href="#" class="btn-outline-white py-3 px-8 rounded-full font-bold text-lg border-2 shadow-lg transform transition duration-300 hover:scale-105">
                        <i class="fas fa-headset mr-2"></i>Hubungi Kami
                    </a>
                </div>
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