import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb', // Atau 'pusher' jika VITE_PUSHER_* vars yang utama, tapi 'reverb' lebih eksplisit
    key: import.meta.env.VITE_REVERB_APP_KEY, // Mengambil dari .env
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT, // Digunakan untuk koneksi wss (secure)
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'], // Aktifkan transport WebSocket dan WebSocket Secure
    // cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER, // Biasanya tidak perlu untuk Reverb
});
window.Echo.channel('apoteker-channel') // Pastikan nama channel sesuai dengan broadcastOn() di Event PHP Anda
    .listen('.order.baru', (e) => { // Pastikan nama event sesuai dengan broadcastAs() di Event PHP Anda (perhatikan titik di depan)
        console.log('Event order.baru diterima di frontend:', e); // Debugging: cek di console browser

        // Dispatch event Livewire untuk memicu refresh komponen OrderDashboard.
        // Karena `handleNewOrderReceived` sudah memanggil `resetPage()` yang memicu re-render,
        // cukup pastikan event ini terkirim.
        // $this->dispatch('$refresh') di Livewire.
        // Jika Anda ingin memicu event kustom, definisikan di komponen Livewire `getListeners()`
        Livewire.dispatch('orderRefresh'); // Contoh: dispatch event kustom ke Livewire
    });