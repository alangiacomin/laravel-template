# Laravel Template

Laravel 13 · PHP 8.4 · Inertia + React + TypeScript · Vite · Reverb · PHPUnit · Pint · Biome.

## Avvio rapido

Requisiti: PHP `^8.4`, Composer, Bun, un database (SQLite, MySQL o PostgreSQL).

```bash
composer install
cp .env.example .env        # poi imposta DB_*
php artisan key:generate
php artisan migrate
bun install
composer run dev            # server, queue, Reverb e Vite insieme
```

## Comandi utili

| Cosa | Comando |
| --- | --- |
| Ambiente di sviluppo | `composer run dev` |
| Test backend | `composer test` |
| Pint + PHPStan | `composer lint` |
| Typecheck + Biome + Vitest | `bun run check` |
| Correggi lint frontend | `bun run lint:fix` |
| Genera tipi TS da PHP | `php artisan typescript:transform` |
| Build frontend | `bun run build` |
| Cache/config strani | `php artisan optimize:clear` |

Dettagli e "dove metto le cose": [docs/development.md](docs/development.md).

## Per agenti AI

[AGENTS.md](AGENTS.md) e [docs/ai/](docs/ai/README.md).

## Licenza

[MIT](https://opensource.org/licenses/MIT)
