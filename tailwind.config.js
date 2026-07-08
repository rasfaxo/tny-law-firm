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
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                navy: {
                    dark: '#0F1E3A',
                    primary: '#1E3A5F',
                },
                accent: {
                    blue: '#2563EB',
                },
                success: {
                    green: '#16A34A',
                },
                warning: {
                    amber: '#F59E0B',
                },
                error: {
                    red: '#DC2626',
                },
                background: {
                    light: '#F8FAFC',
                }
            }
        },
    },

    plugins: [forms],
};
