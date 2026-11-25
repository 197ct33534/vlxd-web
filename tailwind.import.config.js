import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import containerQueries from '@tailwindcss/container-queries';
import { content } from './tailwind.shared.js';

// Luồng import Excel trên mobile: import-invoice-mobile,
// import-sheet-selection-mobile, import-result-mobile.
/** @type {import('tailwindcss').Config} */
export default {
    content,
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                primary: '#2563eb',
                'background-light': '#f8fafc',
                'background-dark': '#0f172a',
                'sidebar-light': '#ffffff',
                'sidebar-dark': '#1e293b',
                // Dùng bởi <x-sidebar /> (qua <x-mobile-sidebar />).
                'text-light': '#212529',
                'text-dark': '#f5f7f8',
                'container-light': '#ffffff',
                'container-dark': '#1a242d',
                'border-light': '#e9ecef',
                'border-dark': '#2c3a47',
            },
            boxShadow: {
                subtle: '0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)',
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                display: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [forms, typography, containerQueries],
};
