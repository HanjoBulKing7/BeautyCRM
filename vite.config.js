import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react'; // ⬅️ IMPORTANTE

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.jsx',
                'resources/css/horizontal.css',
                'resources/js/app.js',
                'resources/js/horizontal.js',
                'resources/js/script.js',
                'resources/js/lenis.js',
                'resources/css/styles.css',
                'resources/css/responsive.css'
            ],
            refresh: true,
        }),
        react(), // ⬅️ AGREGAR EL PLUGIN DE REACT
    ],
});
