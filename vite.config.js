import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/echo.js'
            ],
            refresh: true,
        }),
        
    ],
    resolve: {
        alias: {
            // Allow clean imports like "@/components/..." from resources/js
            '@': path.resolve(__dirname, 'resources/js'),
            
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    echo: ['laravel-echo', 'pusher-js'],
                },
            },
        },
    },
});
