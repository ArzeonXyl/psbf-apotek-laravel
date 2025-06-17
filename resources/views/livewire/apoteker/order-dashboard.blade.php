<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Daftar Order Baru Masuk (Apoteker)</h2>
        {{-- Tombol refresh manual jika diperlukan --}}
        <button wire:click="$refresh" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
            <svg wire:loading wire:target="$refresh" class="animate-spin h-4 w-4 text-gray-600 inline-block mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Refresh Manual
        </button>
    </div>


    {{-- Tempat untuk notifikasi (menggunakan Alpine.js) --}}
    <div x-data="{ open: false, message: '', type: 'info' }"
         @notify.window="message = $event.detail.message; type = $event.detail.type; open = true; setTimeout(() => open = false, 6000)"
         x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         x-cloak
         class="fixed top-5 right-5 z-50 w-full max-w-xs p-4 rounded-md shadow-lg"
         :class="{
             'bg-green-500 text-white': type === 'success',
             'bg-red-500 text-white': type === 'error',
             'bg-blue-500 text-white': type === 'info',
             'bg-yellow-500 text-black': type === 'warning',
         }"
         role="alert">
        <div class="flex items-center">
            <div class="py-1"><svg class="fill-current h-6 w-6 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
            <div>
                <p class="font-bold" x-text="message"></p>
            </div>
            <button @click="open = false" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-current rounded-lg focus:ring-2 focus:ring-gray-400 p-1.5 hover:bg-gray-200/50 inline-flex h-8 w-8" aria-label="Close">
                <span class="sr-only">Dismiss</span><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    </div>

    <div>
        @if($newOrders->count() > 0)
            <div class="space-y-4">
                @foreach($newOrders as $order)
                    <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 border border-gray-200" wire:key="order-{{ $order->id }}">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-indigo-700">Order ID: #{{ $order->id }}</h3>
                                <p class="text-sm text-gray-600">Pelanggan: <span class="font-medium">{{ $order->customer_name ?? 'Tidak ada nama' }}</span></p>
                                {{-- PERBAIKAN DI SINI: Gunakan operator nullsafe ?-> --}}
                                <p class="text-sm text-gray-500">Kasir: <span class="font-medium">{{ $order->user?->name ?? 'N/A' }}</span></p>
                                <p class="text-sm text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y, H:i') }}</p>
                                <p class="text-sm text-gray-500">Status: <span class="font-semibold uppercase">{{ $order->status }}</span></p>
                            </div>
                            <div class="mt-2 sm:mt-0">
                                <span class="text-xl font-bold text-gray-800">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <h4 class="font-medium text-gray-700 mb-2">Item Obat:</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                @foreach($order->items as $item)
                                    <li class="flex justify-between">
                                        <span>
                                            {{-- PERBAIKAN DI SINI: Gunakan operator nullsafe ?-> --}}
                                            {{ $item->obat?->NAMA_OBAT ?? 'Obat tidak ditemukan' }}
                                            <span class="text-xs text-gray-500">(x{{ $item->quantity }})</span>
                                        </span>
                                        <span>Rp{{ number_format($item->sub_total, 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @if($order->notes)
                        <div class="mt-3 bg-yellow-50 border border-yellow-200 p-3 rounded-md">
                            <p class="text-sm text-yellow-700"><strong class="font-semibold">Catatan:</strong> {{ $order->notes }}</p>
                        </div>
                        @endif
                        <div class="mt-6 flex justify-end">
                            <button
                                wire:click="accOrder({{ $order->id }})"
                                wire:confirm="Anda yakin ingin menyelesaikan dan mengurangi stok untuk order ID #{{ $order->id }}?"
                                wire:loading.attr="disabled"
                                wire:target="accOrder({{ $order->id }})"
                                class="px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-60 flex items-center">
                                <svg wire:loading wire:target="accOrder({{ $order->id }})" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="accOrder({{ $order->id }})">ACC & Proses Order</span>
                                <span wire:loading wire:target="accOrder({{ $order->id }})">Memproses...</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $newOrders->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Order Baru</h3>
                <p class="mt-1 text-sm text-gray-500">Order yang baru masuk dari kasir akan muncul di sini secara otomatis.</p>
            </div>
        @endif
    </div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Daftar Order Baru Masuk (Apoteker)</h2>

            {{-- === TAMBAHKAN TOMBOL TES DI SINI === --}}
            <button wire:click="sendTestNotificationToKasir" 
                    class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700">
                Kirim Notifikasi Tes ke Kasir
            </button>
            {{-- ====================================== --}}
        </div>
</div>
{{-- resources/views/livewire/apoteker/order-dashboard.blade.php --}}
