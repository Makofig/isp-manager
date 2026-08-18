import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/views/components/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['JetBrains Mono', 'monospace', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'monospace'],
            },
            colors: {
                // InterSys Dark Palette
                dark: {
                    DEFAULT: '#0B0F14',
                    bg: '#0B0F14',
                    surface: '#11161D',
                    elevated: '#171D25',
                    border: '#252D36',
                },
                accent: {
                    DEFAULT: '#58A6FF',
                    hover: '#79B8FF',
                },
                muted: {
                    DEFAULT: '#5D6670',
                    light: '#8B949E',
                },
                // Text colors
                'text-primary': '#E6EDF3',
                'text-secondary': '#8B949E',
                // Status colors
                success: '#3FB950',
                warning: '#D29922',
                danger: '#F85149',
            },
            ringColor: {
                'dark-border': '#252D36',
                'accent': '#58A6FF',
                'danger': '#F85149',
                'warning': '#D29922',
            },
            transitionDuration: {
                '150': '150ms',
            },
            maxWidth: {
                '8xl': '1600px',
            },
        },
    },

    plugins: [forms, typography],
};
