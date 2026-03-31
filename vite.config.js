// vite.config.js (SIN React)
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        // ✅ CSS principal
        'resources/css/app.css',
        'resources/css/components.css',   // 👈 tu dark mode + overrides
        'resources/css/horizontal.css',
        'resources/css/styles.css',
        'resources/css/responsive.css',
          'resources/css/ui.css',   // 👈 agrega esto
        'resources/css/agendarcita.css',


        // ✅ JS (vanilla)
        'resources/js/app.js',            // 👈 deja SOLO app.js (borra app.jsx)
        'resources/js/horizontal.js',
        'resources/js/script.js',
        'resources/js/lenis.js',
      ],
      refresh: true,
    }),
  ],

});
