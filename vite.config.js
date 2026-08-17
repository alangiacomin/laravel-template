import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import {execSync} from 'node:child_process';
import path from 'node:path';

const ENTITY_ROOT = path.resolve('../app');
let debounceTimer = null;
let ziggyDebounceTimer = null;

function runTypescriptTransform() {
  if (debounceTimer) {
    clearTimeout(debounceTimer);
  }

  debounceTimer = setTimeout(() => {
    try {
      execSync('php artisan typescript:transform', {
        stdio: 'inherit',
      });
    } catch (error) {
      console.error('Errore durante typescript:transform');
    }
  }, 250);
}

function runZiggyGenerate() {
  if (ziggyDebounceTimer) {
    clearTimeout(ziggyDebounceTimer);
  }

  ziggyDebounceTimer = setTimeout(() => {
    try {
      execSync('php artisan ziggy:generate', {
        stdio: 'inherit',
      });
    } catch (error) {
      console.error('Errore durante ziggy:generate');
    }
  }, 250);
}

export default defineConfig({
  plugins: [
    react({
      babel: {
        plugins: ['babel-plugin-react-compiler'],
      },
    }),
    laravel({
      input: [
        'resources/css/vendor.css',
        'resources/css/app.css',
        'resources/js/index.tsx',
        // 'resources/js/app.js'
      ],
      refresh: true,
    }),
    {
      name: 'spatie-typescript-transformer',

      buildStart() {
        runTypescriptTransform();
      },

      configureServer(server) {
        server.watcher.add(ENTITY_ROOT);

        server.watcher.on('change', (file) => {
          const normalized = file.replace(/\\/g, '/');
          const isPhp = normalized.endsWith('.php');
          if (isPhp
            && (
              // normalized.includes('/Domain/Entities/')
              normalized.includes('/Application/Data/')
              || normalized.includes('/Presentation/Http/Requests/')
              || normalized.includes('/app/Enums/')
            )) {
            console.log(`File changed: ${file}`);
            runTypescriptTransform();
          }
        });
      },
    },
    {
      name: 'ziggy-generate',

      buildStart() {
        runZiggyGenerate();
      },

      configureServer(server) {
        // osserviamo direttamente la cartella routes
        server.watcher.add(path.resolve('../routes'));

        server.watcher.on('change', (file) => {
          const normalized = file.replace(/\\/g, '/');

          const isPhp = normalized.endsWith('.php');
          const isRoutes =
            normalized.includes('/routes/');

          if (isPhp && isRoutes) {
            console.log(`Route modificata: ${file}`);
            runZiggyGenerate();
          }
        });
      },
    }

  ],
  build: {
    rollupOptions: {
      output: {
        chunkFileNames: 'js/[name]-[hash].js',
        entryFileNames: 'js/[name]-[hash].js',
        codeSplitting: {
          groups: [
            {
              name: 'react-vendor',
              // minSize: 100000, // 100KB
              // maxSize: 250000, // 250KB
              test: /node_modules\/react|react-dom|@inertiajs/,
              priority: 20,
            },
            {
              name: 'vendor',
              // minSize: 100000, // 100KB
              // maxSize: 250000, // 250KB
              test: /node_modules/,
              priority: 10,
            },
          ],
        },
        // manualChunks: {
        //   vendor: [
        //     'react',
        //     'react-dom',
        //     '@inertiajs/react',
        //   ],
        // },
      },
    },
    // Aggiungere una gestione migliorata delle risorse statiche
    // assetsInlineLimit: 4096, // Per caricare file di dimensioni maggiori come file esterni
    minify: 'esbuild', // Usa Terser per una migliore compressione del codice
  },
  // server: {
  //   watch: {
  //     ignored: [
  //       '**',
  //       '!resources/js/**/*',
  //       '!vite.config.js'
  //     ]
  //   }
  // }
});
