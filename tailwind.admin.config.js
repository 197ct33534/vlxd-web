import forms from '@tailwindcss/forms';
import containerQueries from '@tailwindcss/container-queries';
import { content } from './tailwind.shared.js';

// Theme của layouts/dashboard.blade.php (trước đây khai báo inline cho CDN).
/** @type {import('tailwindcss').Config} */
export default {
    content,
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                primary: '#007BFF',
                'background-light': '#F5F6F8',
                'background-dark': '#101922',
                'text-light': '#212529',
                'text-dark': '#f5f7f8',
                'container-light': '#ffffff',
                'container-dark': '#1a242d',
                'border-light': '#e9ecef',
                'border-dark': '#2c3a47',
            },
            fontFamily: {
                display: ['Inter', 'sans-serif'],
            },
            borderRadius: {
                DEFAULT: '0.5rem',
                lg: '0.75rem',
                xl: '1rem',
                full: '9999px',
            },
            boxShadow: {
                subtle: '0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)',
            },
        },
    },
    plugins: [forms, containerQueries],
};
