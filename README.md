# Laravel Template

Personal Laravel template designed to kick off new projects with a consistent technical foundation, ready for
development.

## Why this template

- Reduces the bootstrap time for a new project.
- Provides a modern Laravel stack with Inertia + React + TypeScript.
- Includes preconfigured tooling for local development, builds, and testing.

## Included stack

- Laravel 13
- PHP 8.4
- Inertia.js (server + React client)
- Vite
- TypeScript
- Reverb (websocket)
- PHPUnit + Laravel Pint
- Bun/NPM for frontend dependencies

## Requirements

- PHP `^8.4`
- Composer
- Bun (recommended) or NPM
- Local database (e.g. SQLite, MySQL, or PostgreSQL)

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
bun install
bun run build
php artisan serve
```

## Useful commands

```bash
# full development environment (server, queue, reverb, vite)
composer run dev

# frontend only
bun run dev
bun run build

# test
composer test
composer test-coverage

# code style
./vendor/bin/pint
```

## Project structure (high-level)

- `app/` application logic
- `routes/` route definitions
- `resources/` frontend (React/TS, views, and assets)
- `config/` configuration files
- `database/` migrations, factories, and seeders
- `tests/` test suite

## Documentation

- Full setup: [`docs/setup.md`](docs/setup.md)
- Development workflow: [`docs/development.md`](docs/development.md)

## Contributing

If this template is used by a team, you can add a `CONTRIBUTING.md` file with PR/branching rules.

## License

This project is released under the [MIT](https://opensource.org/licenses/MIT) license.
