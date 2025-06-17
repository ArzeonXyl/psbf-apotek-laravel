<?php

namespace App\Livewire\Apoteker;

use App\Models\Order;
use App\Models\Obat;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\OrderStatusUpdated; // Event ini di-import dan digunakan
// use App\Events\NewOrderCreated; // Event ini tidak digunakan langsung di sini, bisa dihapus atau tetap sebagai referensi


class OrderDashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // Tema paginasi untuk tampilan paginasi Livewire

    /**
     * Mendefinisikan listener untuk event Livewire, termasuk event yang diterima dari Echo/Reverb.
     *
     * @return array
     */
    public function getListeners(): array
    {
        return [
            // Mendengarkan event 'order.baru' dari channel 'apoteker-channel' via PHP Broadcast.
            // Event ini dipicu ketika order baru dibuat (misalnya oleh kasir melalui Filament).
            'echo:apoteker-channel,order.baru' => 'handleNewOrderReceived',

            // Mendengarkan event kustom 'orderRefresh' yang dipicu dari JavaScript di frontend.
            // Ketika event ini diterima, Livewire akan memicu re-render keseluruhan komponen.
            'orderRefresh' => '$refresh',
        ];
    }

    /**
     * Metode mount() dipanggil satu kali saat komponen Livewire pertama kali diinisialisasi.
     * Tidak ada logika inisialisasi data di sini karena metode render() akan menanganinya.
     *
     * @return void
     */
    public function mount(): void
    {
        // Data order akan dimuat secara otomatis oleh metode render()
        // saat komponen pertama kali dimuat atau di-render.
        // Tidak ada properti state yang perlu diinisialisasi untuk data order di mount()
        // karena `render()` sudah menangani pengambilan data paginasi secara efisien.
    }

    /**
     * Menangani event 'order.baru' yang diterima melalui Livewire Echo.
     * Metode ini dipicu ketika event order baru di-broadcast dari server.
     *
     * @param array $eventData Payload data yang dikirim bersama event.
     * @return void
     */
    public function handleNewOrderReceived(array $eventData): void
    {
        Log::info('Livewire ApotekerOrderDashboard menerima event order.baru:', $eventData);

        // Memicu notifikasi di frontend untuk memberi tahu pengguna ada order baru.
        $this->dispatch('notify', message: 'Order baru diterima: ID #' . ($eventData['order']['id'] ?? 'N/A'), type: 'info');

        // Mengatur ulang paginasi ke halaman pertama agar order terbaru dapat langsung terlihat.
        // Pemanggilan `resetPage()` ini akan secara otomatis memicu re-render komponen,
        // sehingga daftar order akan diperbarui di tampilan.
        $this->resetPage();
    }

    /**
     * Memproses 'Acc' (Accept) sebuah order oleh apoteker.
     * Metode ini menangani validasi, pengurangan stok obat, dan perubahan status order.
     *
     * @param int $orderId ID dari order yang akan di-Acc.
     * @return void
     */
    public function accOrder(int $orderId): void
    {
        Log::info("Apoteker mencoba ACC order ID: {$orderId}");

        DB::beginTransaction(); // Memulai transaksi database
        try {
            // Memuat order beserta item-itemnya dan relasi obat untuk update stok.
            // Relasi user (kasir) juga dimuat untuk informasi tambahan (opsional).
            $order = Order::with(['items.obat', 'user'])->find($orderId);

            // Memeriksa apakah order ditemukan di database.
            if (!$order) {
                Log::error("ACC GAGAL: Order ID {$orderId} tidak ditemukan.");
                $this->dispatch('notify', message: "Error: Order tidak ditemukan!", type: 'error');
                DB::rollBack(); // Rollback transaksi jika order tidak ditemukan
                return;
            }

            // Memeriksa status order; hanya order dengan status 'baru' yang bisa di-Acc.
            if ($order->status !== 'baru') {
                Log::warning("ACC INFO: Order ID {$orderId} sudah diproses atau statusnya bukan 'baru'. Status saat ini: {$order->status}");
                $this->dispatch('notify', message: "Info: Order ini statusnya bukan 'baru' lagi.", type: 'warning');
                DB::rollBack(); // Rollback transaksi karena order tidak perlu diproses
                return;
            }

            // 1. Mengurangi stok obat untuk setiap item dalam order.
            foreach ($order->items as $item) {
                $obat = $item->obat; // Menggunakan relasi 'obat' yang sudah di-load dari OrderItem

                // --- PENTING: Periksa Penamaan Kolom Database vs Model ---
                // Pastikan nama kolom 'ID_OBAT', 'NAMA_OBAT', 'JUMLAH_STOCK' di model Obat Anda
                // sesuai dengan nama kolom aktual di tabel 'obats' di database.
                // Jika database menggunakan snake_case (misal: id_obat, nama_obat, jumlah_stock),
                // maka Anda harus mengaksesnya dengan snake_case ($obat->id_obat, $obat->nama_obat, $obat->jumlah_stock).
                // Jika memang menggunakan UPPERCASE di database, pastikan model Obat Anda
                // sudah dikonfigurasi dengan properti seperti $primaryKey (jika ID bukan 'id'),
                // $incrementing (jika ID tidak auto-increment), dan $keyType (jika ID bukan integer).

                // Memeriksa apakah obat ditemukan untuk item ini.
                if (!$obat) {
                    Log::error("ACC GAGAL: Obat dengan ID {$item->ID_OBAT} tidak ditemukan untuk item order ID {$item->id}.");
                    $this->dispatch('notify', message: "Error: Item obat tidak ditemukan dalam database!", type: 'error');
                    DB::rollBack(); // Rollback transaksi jika obat tidak ditemukan
                    return;
                }

                // Memeriksa ketersediaan stok.
                if ($obat->JUMLAH_STOCK >= $item->quantity) {
                    // Menggunakan metode `decrement()` untuk operasi pengurangan stok yang atomik dan aman.
                    $obat->decrement('JUMLAH_STOCK', $item->quantity);
                    Log::info("Stok obat ID {$obat->ID_OBAT} ({$obat->NAMA_OBAT}) dikurangi {$item->quantity}. Stok baru: {$obat->JUMLAH_STOCK}");
                } else {
                    Log::error("ACC GAGAL: Stok obat ID {$obat->ID_OBAT} ({$obat->NAMA_OBAT}) tidak cukup. Dibutuhkan: {$item->quantity}, Tersedia: {$obat->JUMLAH_STOCK}");
                    $this->dispatch('notify', message: "Error: Stok {$obat->NAMA_OBAT} tidak cukup!", type: 'error');
                    DB::rollBack(); // Rollback transaksi jika stok tidak mencukupi
                    return;
                }
            }

            // 2. Mengubah status order menjadi 'selesai'.
            $order->status = 'selesai';
            $order->save();
            Log::info("Order ID {$orderId} status diubah menjadi 'selesai'.");

            DB::commit(); // Meng-commit transaksi jika semua operasi berhasil
            Log::info("Order ID {$orderId} berhasil di-ACC.");
            $this->dispatch('notify', message: "Order ID #{$orderId} berhasil diproses!", type: 'success');

            // --- PENTING: BROADCAST EVENT UNTUK REFRESH FILAMENT ---
            // Setelah order berhasil di-Acc dan disimpan, memicu event `OrderStatusUpdated`.
            // Event ini akan memberitahu panel admin Filament (kasir) untuk me-refresh tabel Order.
            event(new OrderStatusUpdated($order));
            Log::info("Event OrderStatusUpdated di-broadcast untuk Order ID: {$order->id} setelah ACC.");

            // Komponen Livewire ini (OrderDashboard Apoteker) akan otomatis di-render ulang
            // karena order yang baru saja di-Acc akan hilang dari daftar 'baru' yang ditampilkan.

        } catch (\Exception $e) {
            DB::rollBack(); // Melakukan rollback transaksi jika terjadi exception
            Log::error("Exception saat ACC order ID {$orderId}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->dispatch('notify', message: "Terjadi error sistem saat memproses order.", type: 'error');
        }
    }

    /**
     * Merender tampilan komponen Livewire.
     * Metode ini mengambil dan menyiapkan data yang akan ditampilkan di tampilan.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        // Mengambil daftar order dengan status 'baru', diurutkan dari yang paling baru.
        // Relasi 'items' (item order) dan 'obat' (detail obat), serta 'user' (kasir)
        // dimuat secara eager-load untuk memastikan data terkait tersedia di tampilan tanpa N+1 query.
        $newOrders = Order::where('status', 'baru')
                            ->with(['items.obat', 'user']) // Pastikan relasi 'user' ada di model Order Anda
                            ->latest() // Mengurutkan berdasarkan kolom 'created_at' secara descending (terbaru dulu)
                            ->paginate(5); // Menerapkan paginasi dengan 5 order per halaman

        return view('livewire.apoteker.order-dashboard', [
            'newOrders' => $newOrders,
        ])->layout('layouts.dashboard-layout'); // Menggunakan layout Blade `resources/views/layouts/guest.blade.php`.
                                    // PENTING: Pastikan file `resources/views/layouts/guest.blade.php` ada.
                                    // Jika Anda menggunakan Laravel Breeze, ini adalah layout default untuk halaman tanpa login.
                                    // Jika tidak ada `guest.blade.php`, sesuaikan dengan layout non-autentikasi Anda.
    }

}