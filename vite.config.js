import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/live2d/live2d-viewer.js',
                'resources/js/live2d/live2d-start.js',
                "resources/js/pages/login_register.js"
            ],
            refresh: true,
        }),
    ],
    server: {
        https: false
    },
});
