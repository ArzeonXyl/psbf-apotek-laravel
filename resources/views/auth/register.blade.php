<x-guest-layout>
    {{-- Container utama untuk dua card (form di kiri, deskripsi di kanan) --}}
    <div class="w-full max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 rounded-xl shadow-2xl overflow-hidden">

        {{-- Kolom Kiri: Form Registrasi (Latar Belakang Putih) --}}
        <div class="bg-white p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col justify-center">
            <div class="mb-8 lg:mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-2">
                    Register Akun
                </h1>
                <p class="text-md sm:text-lg text-gray-500">Silakan daftar untuk mengakses sistem</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5 sm:space-y-6">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required autofocus autocomplete="name"
                        class="input-custom w-full" value="{{ old('name') }}">
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-danger" />
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" required autocomplete="username"
                        class="input-custom w-full" value="{{ old('email') }}">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-danger" />
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Role</label>
                    <select id="role" name="role" required
                        class="input-custom w-full">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="apoteker" {{ old('role') == 'apoteker' ? 'selected' : '' }}>Apoteker</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2 text-sm text-danger" />
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        class="input-custom w-full">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-danger" />
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                        class="input-custom w-full">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-danger" />
                </div>

                {{-- Tombol Submit dan Link Kembali ke Login --}}
                <div class="flex flex-col sm:flex-row items-center justify-between mt-8 sm:mt-10 space-y-4 sm:space-y-0">
                    <a href="{{ route('login') }}" class="text-sm sm:text-base link-text-primary">
                        <i class="fas fa-arrow-left mr-1"></i>Sudah punya akun?
                    </a>
                    <button type="submit" class="btn-primary w-full sm:w-auto">
                        <i class="fas fa-user-plus mr-1"></i>Daftar
                    </button>
                </div>
            </form>
        </div>

        {{-- Kolom Kanan: Deskripsi dengan Background Gradient Hijau yang "Nendang" --}}
        <div class="bg-gradient-to-br from-primary to-primary-dark p-8 md:p-12 flex flex-col justify-center items-center text-white text-center">
            {{-- Ikon FontAwesome Apotek --}}
            <i class="fas fa-clinic-medical text-6xl mb-6"></i>

            <h2 class="text-4xl font-extrabold mb-4 leading-tight">
                Sistem Apotek Modern
            </h2>
            <p class="text-lg opacity-90 leading-relaxed">
                Kelola stok obat, transaksi, dan data pelanggan dengan mudah dan efisien.
                Sistem ini dirancang untuk mempermudah operasional apotek Anda.
            </p>
            <p class="mt-6 text-base opacity-80">
                Daftar sekarang untuk memulai perjalanan Anda.
            </p>
        </div>
    </div>
</x-guest-layout>