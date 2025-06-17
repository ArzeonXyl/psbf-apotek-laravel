<div
    class="w-full max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 rounded-xl shadow-2xl overflow-hidden"
    x-data="{ showRegister: @entangle('showRegisterForm') }"
>

    {{-- Kolom Form (Login/Register) --}}
    <div
        class="bg-white p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col justify-center
        transition-all duration-700 ease-in-out"
        :class="showRegister ? 'md:order-first' : 'md:order-last'"
    >
        {{-- Pesan Sukses/Error --}}
        @if(session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Form Register --}}
        <div
            x-show="showRegister"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 transform -translate-x-10"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-400"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform -translate-x-10"
            class="w-full"
            style="display: none;"
        >
            <div class="mb-8 lg:mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-2">Register Akun</h1>
                <p class="text-md sm:text-lg text-gray-500">Silakan daftar untuk mengakses sistem</p>
            </div>

            <form wire:submit.prevent="register" class="space-y-5 sm:space-y-6">
                <div>
                    <label for="register-name" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input id="register-name" type="text" required autofocus autocomplete="name"
                        class="input-custom w-full" wire:model.defer="name">
                    @error('name') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="register-email" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Email</label>
                    <input id="register-email" type="email" required autocomplete="username"
                        class="input-custom w-full" wire:model.defer="email">
                    @error('email') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="register-role" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Role</label>
                    <select id="register-role" required class="input-custom w-full" wire:model.defer="role">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="apoteker">Apoteker</option>
                    </select>
                    @error('role') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="register-password" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Password</label>
                    <input id="register-password" type="password" required autocomplete="new-password"
                        class="input-custom w-full" wire:model.defer="password">
                    @error('password') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="register-password_confirmation" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input id="register-password_confirmation" type="password" required autocomplete="new-password"
                        class="input-custom w-full" wire:model.defer="password_confirmation">
                    @error('password_confirmation') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between mt-8 sm:mt-10 space-y-4 sm:space-y-0">
                    <button type="button" class="text-sm sm:text-base link-text-primary" @click="showRegister = false">
                        <i class="fas fa-arrow-left mr-1"></i>Sudah punya akun?
                    </button>
                    <button type="submit" class="btn-primary w-full sm:w-auto">
                        <i class="fas fa-user-plus mr-1"></i>Daftar
                    </button>
                </div>
            </form>
        </div>

        {{-- Form Login --}}
        <div
            x-show="!showRegister"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 transform translate-x-10"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-400"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-10"
            class="w-full"
            style="display: none;"
        >
            <div class="mb-8 lg:mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-2">Login Akun</h1>
                <p class="text-md sm:text-lg text-gray-500">Silakan login untuk mengakses sistem</p>
            </div>

            <form wire:submit.prevent="login" class="space-y-5 sm:space-y-6">
                <div>
                    <label for="login-email" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Email</label>
                    <input id="login-email" type="email" required autofocus autocomplete="username"
                        class="input-custom w-full" wire:model.defer="loginEmail">
                    @error('loginEmail') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="login-password" class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Password</label>
                    <input id="login-password" type="password" required autocomplete="current-password"
                        class="input-custom w-full" wire:model.defer="loginPassword">
                    @error('loginPassword') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="block">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary" name="remember" wire:model.defer="remember">
                        <span class="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between mt-8 sm:mt-10 space-y-4 sm:space-y-0">
                    <button type="button" class="text-sm sm:text-base link-text-primary" @click="showRegister = true">
                        Belum punya akun?
                    </button>
                    <button type="submit" class="btn-primary w-full sm:w-auto">
                        <i class="fas fa-sign-in-alt mr-1"></i>Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Kolom Deskripsi --}}
    <div
        class="bg-gradient-to-br from-primary to-primary-dark p-8 md:p-12 flex flex-col justify-center items-center text-white text-center
        transition-all duration-700 ease-in-out"
        :class="showRegister ? 'md:order-last' : 'md:order-first'"
    >
        <i class="fas fa-clinic-medical text-6xl mb-6"></i>

        <h2 class="text-4xl font-extrabold mb-4 leading-tight">Sistem Apotek Modern</h2>
        <p class="text-lg opacity-90 leading-relaxed">
            Kelola stok obat, transaksi, dan data pelanggan dengan mudah dan efisien.
            Sistem ini dirancang untuk mempermudah operasional apotek Anda.
        </p>
        <p class="mt-6 text-base opacity-80" x-text="showRegister ? 'Daftar sekarang untuk memulai perjalanan Anda.' : 'Masuk untuk melanjutkan.'"></p>
    </div>
</div>
