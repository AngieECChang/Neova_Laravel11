import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/js/app.js',
        'resources/css/app.css',
        'resources/css/sb-admin-2.css',
        'resources/css/sb-admin-2.min.css',
      ],
      refresh: true, // 支援熱重載
    }),
    // react(),
  ],
});
