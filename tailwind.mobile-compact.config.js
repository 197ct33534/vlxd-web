import forms from '@tailwindcss/forms';
import containerQueries from '@tailwindcss/container-queries';
import { content } from './tailwind.shared.js';
import { mobileColors } from './tailwind.mobile.config.js';

// Giống tailwind.mobile.config.js nhưng bo góc nhỏ hơn:
// auth/login, customer/mobile, invoices/mobile_index, invoices/mobile_show.
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
                DEFAULT: '0.25rem',
                lg: '0.5rem',
                xl: '0.75rem',
                full: '9999px',
            },
            boxShadow: {
                subtle: '0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)',
            },
        },
    },
    plugins: [forms, containerQueries],
};
