// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
        
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Definisi warna primary yang lebih cerah dan pekat untuk gradasi yang "nendang"
                primary: {
                    light: '#00DDAA',    // Varian yang lebih terang
                    DEFAULT: '#00C888',  // Warna awal gradasi
                    dark: '#007F5B',     // Warna akhir gradasi
                },
                secondary: '#10b981', // Tetap pertahankan jika ada penggunaan lain
                accent: '#fbbf24',    // yellow
                danger: '#ef4444',    // red
            },
        },
    },

     plugins: [
        require('@tailwindcss/forms'), // Jika Anda menggunakan plugin forms Tailwind
        // Tambahkan plugin Tailwind lainnya jika ada
        'resources/css/toko.css', 
    ],

};