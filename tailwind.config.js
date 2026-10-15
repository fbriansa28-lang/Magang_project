const plugin = require('tailwindcss/plugin');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        // CATATAN: folder storage/framework/views TIDAK dipindai karena berisi
        // file kompilasi Blade yang bisa terhapus saat dev server berjalan
        // (php artisan view:clear) -> menyebabkan ENOENT pada PostCSS/Vite.
        // Semua kelas sudah tercakup oleh resources/views/**/*.blade.php.
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'sans-serif'],
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),

        plugin(function ({ addVariant }) {
            addVariant('light', '.light &');
        }),
    ],
};