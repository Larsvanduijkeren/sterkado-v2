import {defineConfig} from 'vite'
import laravel from 'laravel-vite-plugin'
import {wordpressPlugin} from '@roots/vite-plugin'

export default defineConfig(({ mode }) => ({
  base: mode === 'development' ? '/' : '/wp-content/themes/websheriff-sage/public/build/',
  assetsInclude: ['**/*.woff', '**/*.woff2', '**/*.ttf', '**/*.eot', '**/*.svg'],
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/editor.css',
        'resources/js/editor.js',
      ],
      refresh: true,
    }),

    wordpressPlugin(),
  ],
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
    manifest: 'manifest.json',
    rollupOptions: {
      output: {
        manualChunks: (id) => {
          if (id.includes('node_modules/jquery')) return 'jquery';
          if (id.includes('node_modules/swiper')) return 'swiper';
          if (id.includes('node_modules/aos')) return 'aos';
          if (id.includes('node_modules/lenis')) return 'lenis';
        },
        assetFileNames: (assetInfo) => {
          const name = assetInfo.name ?? '';
          if (/\.(woff2?|ttf|eot|otf)$/i.test(name)) return 'fonts/[name]-[hash][extname]';
          if (/\.(png|jpe?g|gif|svg|webp)$/i.test(name)) return 'images/[name]-[hash][extname]';
          return 'assets/[name]-[hash][extname]';
        },
      },
    },
  },

  server: {
    host: '127.0.0.1',
    port: 5173,
    strictPort: true,
    cors: true,
    origin: 'http://127.0.0.1:5173',
    hmr: {
      host: '127.0.0.1',
      protocol: 'ws',
    },
  },

  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },
}))
