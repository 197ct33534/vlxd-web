import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/admin.css',
                'resources/css/portal.css',
                'resources/css/mobile.css',
                'resources/css/mobile-compact.css',
                'resources/css/import.css',
                'resources/css/landing.css',
                'resources/js/alpine.js',
            ],
            refresh: true,
        }),
    ],
});
