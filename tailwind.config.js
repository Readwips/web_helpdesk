import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                ink: '#171716',
                canvas: '#F6F6F2',
                muted: '#6D6D67',
                line: '#DEDED8',
                accent: {
                    DEFAULT: '#17715E',
                    dark: '#115646',
                    soft: '#DDF2EB',
                    pale: '#F0F8F5',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                float: '0 18px 50px rgba(23, 23, 22, 0.12)',
            },
        },
    },

    plugins: [forms],
};
