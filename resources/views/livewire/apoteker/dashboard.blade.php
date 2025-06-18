

<div class="space-y-6">

   {{-- CARD LAPORAN TOTAL --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Kartu Total Pendapatan -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="text-green-600 text-3xl mr-4">
            💰
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Total Pendapatan</h2>
            <p class="text-2xl font-bold text-green-600 mt-2">{{$total_pendapatan}}</p>
        </div>
    </div>
    <!-- Kartu Total Pengeluaran Obat -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="text-red-600 text-3xl mr-2">💊</span> Obat Ter-Favorit
        </h2>

        @php
            $icons = ['🥇', '🥈', '🥉'];
        @endphp

        @foreach($obat_favorit as $index => $obat)
            <div class="flex items-center justify-between bg-red-50 hover:bg-red-100 p-4 rounded-lg mb-3 shadow-sm transition">
                <div class="flex items-center space-x-3">
                    <div class="text-2xl">
                        {{ $icons[$index] ?? '⭐' }}
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-red-700">{{ $obat->nama_obat }}</p>
                        <p class="text-sm text-gray-500">Jumlah Order: {{ $obat->jumlah_order }}</p>
                    </div>
                </div>
                <div class="text-gray-400 text-sm">
                    #{{ $index + 1 }}
                </div>
            </div>
        @endforeach
    </div>
</div>
    {{-- GRAFIK DALAM SATU BARIS KECIL --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Grafik Pendapatan --}}
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-md font-semibold text-gray-800 mb-2">Pendapatan Bulanan</h2>
            <canvas id="monthly_income" class="w-full h-40"></canvas>
        </div>

        {{-- Grafik Pengeluaran --}}
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-md font-semibold text-gray-800 mb-2">Pengeluaran Harian</h2>
            <canvas id="daily_stored" class="w-full h-40"></canvas>
        </div>
    </div>


    {{-- TABEL OBAT --}}
    <div class="p-4 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Daftar Obat</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Nama Obat</th>
                        <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Jumlah Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($obats as $obat)
                        <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="border border-gray-200 p-3 text-sm text-gray-700">{{ $obat->ID_OBAT }}</td>
                            <td class="border border-gray-200 p-3 text-sm text-gray-700">{{ $obat->NAMA_OBAT }}</td>
                            <td class="border border-gray-200 p-3 text-sm text-gray-700">{{ $obat->JUMLAH_STOCK }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="border border-gray-200 p-3 text-center text-gray-500">Tidak ada data obat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tombol Logout --}}
        <button wire:click="logout" class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Logout</button>
    </div>

</div>

{{-- CHART SCRIPT --}}
<script>
    const var_chart = document.getElementById('monthly_income');
    const pengeluaranChart = new Chart(var_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels_pendapatan) !!},
            datasets: [{
                label: 'Pendapatan Per-Bulan Obat (Rp)',
                data: {!! json_encode($data_pendapatan) !!},
                backgroundColor: '#4B70F5',
                borderRadius: 5,
                barPercentage: 0.5,
                categoryPercentage: 0.7
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    const pengeluaran_daily = document.getElementById('daily_stored');
    const ctx = pengeluaran_daily.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, pengeluaran_daily.height);
    gradient.addColorStop(0, 'rgba(75, 112, 245, 0.5)');  
    gradient.addColorStop(1, 'rgba(75, 112, 245, 0)');    

    const chary_pengeluaran_daily = new Chart(pengeluaran_daily, {
        type: 'line',
        data: {
            labels: {!! json_encode($label_pengeluaran_daily) !!},
            datasets: [{
                label: 'Pengeluaran Obat Per-Hari',
                data: {!! json_encode($data_pengeluaran_daily) !!},
                borderColor: '#4B70F5',
                backgroundColor: gradient,
                tension: 0.3,
                fill: true, 
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#4B70F5',
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
