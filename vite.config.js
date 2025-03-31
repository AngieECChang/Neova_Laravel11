import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import react from '@vitejs/plugin-react';

export default defineConfig({
  server: {
    host: 'laravel.local',
    port: 5173,
    strictPort: true,
    cors: true, //關鍵設定：允許跨來源
    origin: 'http://laravel.local:5173',
    hmr: {
      protocol: 'ws',
      host: 'laravel.local',
      port: 5173,
    },
  },
  plugins: [
    laravel({
      input: [
        'resources/js/app.js',
        'resources/css/app.css',
        'resources/css/sb-admin-2.css',
        'resources/css/sb-admin-2.min.css',
        'resources/js/hcevaluation/hcevaluation01_medical.js',
      ],
      refresh: true, // 支援熱重載
    }),
    // react(),
  ],
});
