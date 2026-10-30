# Laravel Template

[![CI](https://github.com/alangiacomin/laravel-template/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/alangiacomin/laravel-template/actions/workflows/ci.yml)

Template Laravel pronto per avviare nuovi progetti con una base moderna, coerente e già configurata per lo sviluppo quotidiano.

## Cosa include

- Laravel 13
- PHP 8.4
- Inertia + React + TypeScript
- Vite
- Reverb (WebSocket)
- PHPUnit e Laravel Pint
- routing localizzato per locale (`it`, `en`)
- struttura organizzata per aree e applicazioni

## Requisiti

- PHP `^8.4`
- Composer
- Bun (consigliato) oppure npm
- database locale (SQLite, MySQL o PostgreSQL)

## Avvio rapido

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
bun install
bun run build
php artisan serve
```

Se vuoi avviare tutto l'ambiente di sviluppo in parallelo:

```bash
composer run dev
```

## Documentazione

Indice completo: [docs/README.md](docs/README.md)

- Setup completo: [docs/dev/setup.md](docs/dev/setup.md)
- Sviluppo e convenzioni: [docs/dev/development.md](docs/dev/development.md)
- Inertia.js: [docs/dev/inertia.md](docs/dev/inertia.md)
- Documentazione per agenti AI: [AGENTS.md](AGENTS.md) e [docs/ai/](docs/ai/)
- Documentazione dell'app: [docs/app/](docs/app/README.md)

## Nota importante

Questo template usa una sola lingua di base (`it`), definita in `config/app.php`, e route non prefissate per locale. Le URL sono quindi stabili e vengono costruite con i nomi delle route Laravel/Ziggy, senza un file `config/routes.php`.

Esempi:

- `/`
- `/login`
- `/admin.roles`

Per i dettagli completi, consulta la documentazione in `docs/`.

## Licenza

Questo progetto è distribuito con licenza [MIT](https://opensource.org/licenses/MIT).
