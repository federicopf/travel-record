import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/main.js'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources/js'),
      '@Pages': resolve(__dirname, 'resources/js/Pages'),
      '@Components': resolve(__dirname, 'resources/js/Components'),
      '@Layouts': resolve(__dirname, 'resources/js/Layouts'),
      '@Composables': resolve(__dirname, 'resources/js/Composables'),
      '@Utils': resolve(__dirname, 'resources/js/Utils'),
      '@Stores': resolve(__dirname, 'resources/js/Stores'),
      '@Types': resolve(__dirname, 'resources/js/Types'),
      '@Assets': resolve(__dirname, 'resources/js/Assets'),
    },
  },
  optimizeDeps: {
    include: ['vue', '@inertiajs/vue3', 'pinia'],
  },
})
