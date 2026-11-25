import forms from '@tailwindcss/forms';
import { content } from './tailwind.shared.js';

// Theme của layouts/mobile-customer.blade.php (portal khách /m, tài xế).
/** @type {import('tailwindcss').Config} */
export default {
    content,
    theme: {
        extend: {
            colors: {
                primary: '#007BFF',
                surface: '#F5F6F8',
                ink: '#212529',
            },
            fontFamily: {
                display: ['Be Vietnam Pro', 'sans-serif'],
            },
        },
    },
    plugins: [forms],
};
