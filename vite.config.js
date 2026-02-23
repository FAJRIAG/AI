import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/public-chat.css',
                'resources/js/app.js',
                'resources/js/public-chat.js',
                'resources/js/vip-chat.js',
                'resources/js/sidebar-toggle.js',
            ],
            refresh: true,
        }),
    ],
});
