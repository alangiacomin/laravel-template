# Sviluppo e workflow quotidiano

Questa pagina riassume il flusso di lavoro pratico per usare il template nello sviluppo quotidiano.

## Avviare l'ambiente locale

Per far partire l'intero stack in parallelo:

```bash
composer run dev
```

Questo comando avvia in genere:

- il server Laravel (`php artisan serve`)
- Reverb per WebSocket (`php artisan reverb:start --port=9000`)
- il worker delle queue (`php artisan queue:listen`)
- il server Vite per il frontend (`bun run dev`)

Se vuoi lavorare solo sul frontend:

```bash
bun run dev
```

Oppure con npm:

```bash
npm run dev
```

## Build frontend

Per creare i file di produzione:

```bash
bun run build
```

oppure:

```bash
npm run build
```

## Test

Eseguire la suite di test:

```bash
composer test
```

Con copertura:

```bash
composer test-coverage
```

## Qualità del codice

Formattazione PHP:

```bash
./vendor/bin/pint
```

Lint, formattazione e import del frontend (JS/TS/React) sono gestiti da [Biome](https://biomejs.dev) tramite `biome.json`:

```bash
bun run lint       # controllo
bun run lint:fix   # applica le correzioni sicure
```

In PhpStorm installa il plugin **Biome** dal Marketplace (Settings → Plugins) e attivalo in Settings → Languages & Frameworks → Biome in modalità automatica: avrai gli hint inline anche sulle dipendenze degli hook (`useExhaustiveDependencies`).

## Convenzioni pratiche

- tieni la logica applicativa in `app/`
- tieni il frontend e i tipi in `resources/`
- aggiungi test per le parti critiche o le logiche non banali
- usa le traduzioni in `lang/` per i testi utente e i messaggi di validazione

## Come aggiungere una nuova pagina

Il template usa una struttura a aree e route PHP esplicite. Per creare una nuova pagina, in genere si seguono questi passaggi:

1. scegli la route file corretto (`routes/web.php`, `routes/web_auth.php` o `routes/web_admin.php`)
2. registra la rotta con un nome logico chiaro e un path stabile
3. crea il controller e il componente frontend necessari
4. aggiungi eventuali test

### Esempio di route

```php
Route::get('/example.page', [ExamplePageController::class, 'index'])
    ->name('example.page');
```

Nel frontend, la URL viene costruita con `route('example.page')` senza alcun prefisso di locale.

## Gestione del locale

Il template usa un locale singolo, con default definito in `config/app.php`:

```php
'locale' => env('APP_LOCALE', 'it'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'it'),
```

Questo significa che non esistono URL con prefisso `/it/` o `/en/`; il contenuto visibile è invece gestito tramite i file di traduzione in `lang/it/` e il shared prop `translations` di Inertia.

## Quando aggiungere una nuova area

Se stai costruendo un nuovo modulo, tieni presente che il template favorisce una separazione chiara tra:

- area pubblica (`app/Areas/Main/...`)
- area admin (`app/Areas/Admin/...`)
- autenticazione (`app/Areas/Main/Auth/...`)

In pratica, usa una struttura coerente per i controller e i dati della pagina, senza mescolare logica di dominio e view in modo indiscriminato.

## Checklist rapida prima di aprire una PR

- `composer test` passa
- `./vendor/bin/pint` è stato eseguito
- le route localizzate sono aggiornate se hai aggiunto pagine nuove
- eventuali file di lingua (`lang/`) sono coerenti con i nuovi testi
- il frontend compila senza errori

Questa checklist è sufficiente per mantenere un flusso di lavoro semplice e prevedibile.
