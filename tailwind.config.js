import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                brand: {
                    50: '#eef4ff',
                    100: '#dbe6fe',
                    200: '#bfd3fe',
                    300: '#93b4fd',
                    400: '#6090fa',
                    500: '#3b6df6',
                    600: '#2551eb',
                    700: '#1d3fd8',
                    800: '#1e36af',
                    900: '#1e328a',
                },
            },

            boxShadow: {
                card: '0 1px 2px 0 rgb(16 24 40 / 0.06), 0 1px 3px 0 rgb(16 24 40 / 0.10)',
            },
        },
    },

    plugins: [forms],
};
