import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Import the Vue plugin

export default defineConfig({
    server: {
        host: 'localhost', // production demo domain  ecommerce1.akalin.tech
        port: 3000, // production demo port 443 , dev port 3000
    },
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
