import forms from '@tailwindcss/forms';
import containerQueries from '@tailwindcss/container-queries';
import { content } from './tailwind.shared.js';

// Màn mobile nội bộ, bo góc lớn: invoices/mobile_create, mobile_edit,
// projects/mobile_index, components/customer/app.
export const mobileColors = {
    primary: '#2a8ff4',
    'background-light': '#f5f7f8',
    'background-dark': '#101922',
    'text-light': '#212529',
    'text-dark': '#f5f7f8',
    'container-light': '#ffffff',
    'container-dark': '#1a242d',
    'border-light': '#e9ecef',
    'border-dark': '#2c3a47',
    'card-dark': '#182430',
};

/** @type {import('tailwindcss').Config} */
export default {
    content,
    darkMode: 'class',
    theme: {
        extend: {
            colors: mobileColors,
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
