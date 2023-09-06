import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',

                'resources/_web-assets/style.css',
                'resources/_web-assets/css/font-icons.css',
                'resources/_web-assets/css/swiper.css',
                'resources/_web-assets/css/custom.css',
                'resources/_web-assets/js/functions.bundle.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
