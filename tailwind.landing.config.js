import forms from '@tailwindcss/forms';
import containerQueries from '@tailwindcss/container-queries';
import { content } from './tailwind.shared.js';

// Trang công khai: landing, landing-mobile.
/** @type {import('tailwindcss').Config} */
export default {
    content,
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                primary: '#f97415',
                'construction-orange': '#f97316',
                'background-light': '#f8f7f5',
                'background-dark': '#23170f',
            },
            fontFamily: {
                display: ['Work Sans', 'sans-serif'],
            },
            borderRadius: {
                DEFAULT: '0.5rem',
                lg: '1rem',
                xl: '1.5rem',
                '2xl': '2rem',
                full: '9999px',
            },
            keyframes: {
                'pulse-custom': {
                    '0%, 100%': { transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(249, 115, 22, 0.7)' },
                    '50%': { transform: 'scale(1.05)', boxShadow: '0 0 0 10px rgba(249, 115, 22, 0)' },
                },
                'shake-custom': {
                    '0%, 100%': { transform: 'rotate(0deg)' },
                    '10%, 30%, 50%, 70%, 90%': { transform: 'rotate(-3deg)' },
                    '20%, 40%, 60%, 80%': { transform: 'rotate(3deg)' },
                },
            },
            animation: {
                'pulse-call': 'pulse-custom 2s infinite',
                'shake-call': 'shake-custom 0.5s ease-in-out infinite alternate',
            },
        },
    },
    plugins: [forms, containerQueries],
};
