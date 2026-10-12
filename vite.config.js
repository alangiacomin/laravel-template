import { exec } from "node:child_process";
import path from "node:path";
import react from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

const ENTITY_ROOT = path.resolve("../app");
let debounceTimer = null;
let ziggyDebounceTimer = null;

// Helper asincrono e non bloccante
function runCommand(command, description) {
  return new Promise((resolve) => {
    exec(command, (error, stdout, stderr) => {
      if (error) {
        console.error(
          `❌ Errore durante ${description}:`,
          stderr || error.message,
        );
      } else if (stdout.trim()) {
        console.log(`\n[${description}] ${stdout.trim()}`);
      }
      resolve();
    });
  });
}

function runTypescriptTransform() {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    runCommand("php artisan typescript:transform", "typescript:transform");
  }, 250);
}

function runZiggyGenerate() {
  if (ziggyDebounceTimer) clearTimeout(ziggyDebounceTimer);
  ziggyDebounceTimer = setTimeout(() => {
    runCommand("php artisan ziggy:generate", "ziggy:generate");
  }, 250);
}

export default defineConfig({
  css: {
    transformer: "lightningcss",
  },
  plugins: [
    react({
      babel: {
        plugins: ["babel-plugin-react-compiler"],
      },
    }),
    laravel({
      input: [
        "resources/css/vendor.css",
        "resources/css/app.css",
        "resources/js/index.tsx",
        // 'resources/js/app.js'
      ],
      refresh: true,
    }),
    {
      name: "spatie-typescript-transformer",
      buildStart() {
        runTypescriptTransform();
      },
      configureServer(server) {
        server.watcher.add(ENTITY_ROOT);
        server.watcher.on("change", (file) => {
          const normalized = file.replace(/\\/g, "/");
          if (
            normalized.endsWith(".php") &&
            (normalized.includes("/Application/Data/") ||
              normalized.includes("/Presentation/Http/Requests/") ||
              normalized.includes("/app/Enums/"))
          ) {
            console.log(`File PHP modificato: ${file}`);
            runTypescriptTransform();
          }
        });
      },
    },
    {
      name: "ziggy-generate",
      buildStart() {
        runZiggyGenerate();
      },
      configureServer(server) {
        server.watcher.add(path.resolve("../routes"));
        server.watcher.on("change", (file) => {
          const normalized = file.replace(/\\/g, "/");
          if (normalized.endsWith(".php") && normalized.includes("/routes/")) {
            console.log(`Route modificata: ${file}`);
            runZiggyGenerate();
          }
        });
      },
    },
  ],
  build: {
    rollupOptions: {
      output: {
        chunkFileNames: "js/[name]-[hash].js",
        entryFileNames: "js/[name]-[hash].js",
        codeSplitting: {
          groups: [
            {
              name: "react-vendor",
              // minSize: 100000, // 100KB
              // maxSize: 250000, // 250KB
              test: /node_modules\/react|react-dom|@inertiajs/,
              priority: 20,
            },
            {
              name: "vendor",
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
    // Abilita Rolldown come bundling engine
    rolldownOptions: {
      // Mantiene la compatibilità con la configurazione rollup che hai già definito
    },
    cssMinify: "lightningcss",
    // Aggiungere una gestione migliorata delle risorse statiche
    // assetsInlineLimit: 4096, // Per caricare file di dimensioni maggiori come file esterni
    minify: "esbuild", // Usa Terser per una migliore compressione del codice
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
