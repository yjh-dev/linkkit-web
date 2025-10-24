import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),

    ],
    server: {
        host: true,            // 0.0.0.0 리슨
        port: 5173,
        origin: 'http://58.79.17.72:5173',  // 클라이언트가 참조할 절대 URL
        hmr: { host: '58.79.17.72', port: 5173 }
    }
});
