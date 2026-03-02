import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/main.css', 'resources/js/main.ts'],
      refresh: [
        'resources/views/**/*.blade.php',
        'resources/**/*.css',
        'resources/**/*.js',
        'resources/**/*.ts',
        'resources/**/*.vue',
      ],
    }),
    tailwindcss(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  define: {
    __APP_NAME__: JSON.stringify(process.env.APP_NAME),
  },
  css: {
    modules: {
      localsConvention: 'camelCaseOnly',
    },
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    hmr: {
      host: 'laravel.test',
      clientPort: 5173,
    },
    watch: {
      ignored: ['**/storage/framework/views/**'],
      usePolling: true,
    },
  },
});
