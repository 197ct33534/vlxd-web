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
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                display: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [forms, typography, containerQueries],
};
