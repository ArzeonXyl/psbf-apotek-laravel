#!/bin/bash

# Memberikan pesan bahwa proses dimulai
echo "🚀 Memulai semua service development..."
echo "------------------------------------------"

# Menjalankan semua perintah di background dengan tanda '&'
php artisan serve &
php artisan serve --port=8001 &
php artisan reverb:start &
npm run dev &

# Pesan bahwa semua service telah berjalan
echo "✅ Semua service berhasil dijalankan di background."
echo "   - Artisan server di http://127.0.0.1:8000"
echo "   - Artisan server di http://127.0.0.1:8001"
echo "   - Reverb server berjalan"
echo "   - Vite/NPM dev server berjalan"
echo ""
echo "Tekan Ctrl+C di terminal ini tidak akan menghentikan service."
echo "Gunakan perintah 'killall php' dan 'killall node' untuk berhenti."