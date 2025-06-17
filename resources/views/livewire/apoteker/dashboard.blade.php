
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

    {{-- Tombol logout di sini tidak diperlukan lagi karena sudah ada di sidebar `dashboard-layout` --}}
    <button wire:click="logout" class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Logout</button>
</div>