{{-- resources/views/filament/hooks/reverb-listener-script.blade.php --}}
@if (config('broadcasting.connections.reverb.key')) {{-- Hanya render jika Reverb dikonfigurasi --}}
    <script type="module">
        // Pastikan Echo sudah ada di window object
        if (window.Echo) {
            console.log('ECHO CLIENT FOUND ON FILAMENT PAGE. Attempting to listen...');

            // Listener untuk update status order
            window.Echo.channel('apoteker-channel')
                .listen('.order.status.updated', (event) => { // Perhatikan '.' di depan nama event saat mendengarkan
                    console.log('FILAMENT PAGE RECEIVED EVENT: order.status.updated', event);
                    // Jika log ini muncul, berarti sinyal sudah sampai!
                    // Refresh otomatis dari listener di ListOrders.php seharusnya berjalan.
                });

            // Listener untuk order baru (tes tambahan)
            window.Echo.channel('apoteker-channel')
                .listen('.order.baru', (event) => {
                    console.log('FILAMENT PAGE RECEIVED EVENT: order.baru', event);
                });

            console.log('Successfully subscribed listeners on apoteker-channel from Filament page.');

        } else {
            console.error('ECHO CLIENT NOT FOUND ON FILAMENT PAGE.');
        }
    </script>
@endif