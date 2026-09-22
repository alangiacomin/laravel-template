# Laravel Template

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

- Setup completo: [docs/setup.md](docs/setup.md)
- Sviluppo e convenzioni: [docs/development.md](docs/development.md)

## Nota importante

Questo template usa route localizzate per locale. Le URL non sono statiche ma dipendono dal prefisso del locale e da `config/routes.php`.

Esempio:

- `/it/accedi`
- `/en/login`
- `/it/admin/ruoli`

Per i dettagli completi, consulta la documentazione in `docs/`.

## Licenza

Questo progetto è distribuito con licenza [MIT](https://opensource.org/licenses/MIT).
