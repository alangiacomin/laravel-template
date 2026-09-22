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

Se il progetto dipende da un repository esterno in path (per esempio `alangiacomin/laravel-cqrs`), assicurati che sia presente nella posizione attesa dal `composer.json` prima di eseguire l'installazione.

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

Apri nel browser l'URL mostrato da `php artisan serve` (di solito `http://127.0.0.1:8000`) e verifica che la homepage del locale predefinito sia raggiungibile.

Il template supporta più locale, con il default definito in `config/app.php`:

- `it`
- `en`

Di solito le homepage sono disponibili come:

- `/it`
- `/en`

## Risoluzione rapida dei problemi

- errore di application key: esegui `php artisan key:generate`
- asset mancanti: esegui `bun run build` oppure `npm run build`
- problemi di cache/configurazione: `php artisan optimize:clear`
- problemi di database: controlla `.env` e poi `php artisan migrate`

## Come funziona il routing localizzato

Le route sono configurate in `config/routes.php` e sono prefissate dal locale attivo. Il prefisso dipende dalle lingue supportate in `config/app.php`.

Esempio di configurazione:

```php
return [
    'localized' => [
        'it' => [
            'login' => 'accedi',
            'register' => 'registrati',
            'example.page' => 'pagina-di-esempio',
        ],
        'en' => [
            'login' => 'login',
            'register' => 'register',
            'example.page' => 'example-page',
        ],
    ],
];
```

Le route vere vengono poi registrate in `routes/web.php` e `routes/web_auth.php` usando il valore locale:

```php
foreach (config('app.locales', []) as $locale) {
    Route::prefix($locale)->name($locale.'.')->group(function () use ($locale): void {
        $localizedRoutes = config("routes.localized.$locale");

        Route::get('/'.$localizedRoutes['example.page'], [ExamplePageController::class, 'index'])
            ->name('example.page');
    });
}
```

Questa convenzione produce URL come:

- `/it/pagina-di-esempio`
- `/en/example-page`

Per creare una nuova route localizzata, basta aggiungere la chiave nel file `config/routes.php` per ogni locale e poi registrare la rotta nel file giusto dentro `routes/`.

## File principali da conoscere

- `routes/web.php`: route del frontend principale
- `routes/web_auth.php`: route di autenticazione
- `routes/web_admin.php`: route di admin
- `config/routes.php`: definizione delle URL localizzate
- `config/app.php`: locali supportati e locale di fallback
- `lang/`: file di traduzione

Questi file sono i punti di partenza per aggiungere pagine nuove o nuove funzionalità al template.
