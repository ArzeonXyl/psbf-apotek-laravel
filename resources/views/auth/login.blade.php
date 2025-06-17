<x-guest-layout>
    {{-- Container utama untuk dua card (deskripsi di kiri, form di kanan) --}}
    {{-- Ini adalah div yang sekarang mengontrol lebar max-w-6xl dan layout grid --}}
    <div class="w-full max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 rounded-xl shadow-2xl overflow-hidden">

        {{-- Kolom Kiri: Deskripsi dengan Background Gradient Hijau yang "Nendang" --}}
        {{-- Order md:order-1 agar di mobile tetap di atas (form), tapi di desktop pindah ke kiri --}}
        <div class="bg-gradient-to-br from-primary to-primary-dark p-8 md:p-12 flex flex-col justify-center items-center text-white text-center order-2 md:order-1">
            {{-- Ikon FontAwesome Apotek --}}
            <i class="fas fa-prescription-bottle-alt text-6xl mb-6"></i> {{-- Ikon disesuaikan untuk login --}}

            <h2 class="text-4xl font-extrabold mb-4 leading-tight">
                Selamat Datang Kembali
            </h2>
            <p class="text-lg opacity-90 leading-relaxed">
                Kelola stok obat, transaksi, dan data pelanggan dengan mudah dan efisien.
                Masuk untuk melanjutkan operasional apotek Anda.
            </p>
            <p class="mt-6 text-base opacity-80">
                Apotek Cepat, Sehat Selalu.
            </p>
        </div>

        {{-- Kolom Kanan: Form Login (Latar Belakang Putih) --}}
        {{-- Order md:order-2 agar di mobile tetap di bawah (deskripsi), tapi di desktop pindah ke kanan --}}
        <div class="bg-white p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col justify-center order-1 md:order-2">
            <div class="mb-8 lg:mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-2">
                    Login Akun
                </h1>
                <p class="text-md sm:text-lg text-gray-500">Masuk untuk mengakses sistem</p>
            </div>

            {{-- Session Status (Jika ada pesan sukses/error dari session) --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5 sm:space-y-6">
                @csrf

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" required autofocus autocomplete="username"
                        class="input-custom w-full" value="{{ old('email') }}">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-danger" />
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        class="input-custom w-full">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-danger" />
                </div>

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                        <label for="remember_me" class="ml-2 block text-sm sm:text-base text-gray-700">Ingat Saya</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a class="link-text-primary text-sm sm:text-base" href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>

                {{-- Tombol Submit dan Link ke Register --}}
                <div class="flex flex-col sm:flex-row items-center justify-between mt-8 sm:mt-10 space-y-4 sm:space-y-0">
                    <a href="{{ route('register') }}" class="text-sm sm:text-base link-text-primary">
                        <i class="fas fa-user-plus mr-1"></i>Belum punya akun?
                    </a>
                    <button type="submit" class="btn-primary w-full sm:w-auto">
                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-guest-layout>