# Setup e primo avvio

Questa pagina raccoglie le istruzioni minime per iniziare a usare il template in locale.

## Requisiti

- PHP `^8.4`
- Composer
- Bun (consigliato) oppure npm
- un database locale: SQLite, MySQL o PostgreSQL

## 1) Installare le dipendenze PHP

```bash
composer install
```

## 2) Configurare l'ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Apri poi il file `.env` e imposta almeno:

- la connessione al database (`DB_*`)
- i driver per cache, sessione e queue se diversi dal default
- eventuali variabili specifiche del progetto

## 3) Preparare il database

```bash
php artisan migrate
```

Se vuoi caricare i seed:

```bash
php artisan db:seed
```

## 4) Installare le dipendenze frontend

Con Bun:

```bash
bun install
```

Con npm:

```bash
npm install
```

## 5) Preparare i file frontend

Con Bun:

```bash
bun run build
```

Con npm:

```bash
npm run build
```

## 6) Avviare l'applicazione

Modalità semplice:

```bash
php artisan serve
```

Modalità completa (server web, queue, Reverb e Vite):

```bash
composer run dev
```

## 7) Verifica iniziale

Apri nel browser l'URL mostrato da `php artisan serve` (di solito `http://127.0.0.1:8000`) e verifica che la homepage sia raggiungibile.

Il template usa un locale singolo (default `it`), definito in `config/app.php`, e non aggiunge prefissi di lingua alle URL.

## Risoluzione rapida dei problemi

- errore di application key: esegui `php artisan key:generate`
- asset mancanti: esegui `bun run build` oppure `npm run build`
- problemi di cache/configurazione: `php artisan optimize:clear`
- problemi di database: controlla `.env` e poi `php artisan migrate`

## Come funzionano le route nel template

Le route sono registrate direttamente nei file di routing Laravel:

- `routes/web.php`: frontend principale e fallback
- `routes/web_auth.php`: autenticazione
- `routes/web_admin.php`: amministrazione

Esempio:

```php
Route::get('/example.page', [ExamplePageController::class, 'index'])
    ->name('example.page');

Route::get('/admin.roles', [RoleController::class, 'index'])
    ->name('admin.roles');
```

Non esiste un file `config/routes.php` e non vengono usati prefissi per locale. Per generare URL nel frontend si usano i nomi route con `route('...')` o `route('admin.roles')` in Ziggy.

## File principali da conoscere

- `routes/web.php`: route del frontend principale
- `routes/web_auth.php`: route di autenticazione
- `routes/web_admin.php`: route di admin
- `config/app.php`: locale attivo e fallback
- `lang/`: file di traduzione

Questi file sono i punti di partenza per aggiungere pagine nuove o nuove funzionalità al template.
