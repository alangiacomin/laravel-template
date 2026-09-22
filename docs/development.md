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

Il frontend ha configurazione ESLint pronta in `eslint.config.js`; puoi aggiungere script dedicati se vuoi standardizzare i comandi di linting nel tuo workflow.

## Convenzioni pratiche

- tieni la logica applicativa in `app/`
- tieni il frontend e i tipi in `resources/`
- aggiungi test per le parti critiche o le logiche non banali
- usa le traduzioni in `lang/` per i testi utente e i messaggi di validazione

## Come aggiungere una nuova pagina

Il template usa una struttura orientata a locale e aree. Per creare una nuova pagina, solitamente fai questi passaggi:

1. aggiungi la route localizzata in `config/routes.php`
2. registra la rotta nel file `routes/` corretto
3. crea il controller e la view / componente frontend necessari
4. aggiungi eventuali test

### Esempio di route localizzata

In `config/routes.php`:

```php
'it' => [
    'example.page' => 'pagina-di-esempio',
],
'en' => [
    'example.page' => 'example-page',
],
```

Poi in `routes/web.php`:

```php
Route::get('/'.$localizedRoutes['example.page'], [ExamplePageController::class, 'index'])
    ->name('example.page');
```

Di conseguenza, con i locale attivi, l'URL sarà:

- `/it/pagina-di-esempio`
- `/en/example-page`

Il nome logico della route diventa invece qualcosa del tipo:

- `it.example.page`
- `en.example.page`

L'approccio è così coerente per tutte le pagine pubbliche, admin e autenticazione.

## Gestione dei locale

Il template supporta `it` e `en` come locale dichiarati in `config/app.php`.

La homepage di ogni locale è gestita tramite routing prefissato; in pratica:

```php
Route::prefix($locale)->name($locale.'.')->group(function () {
    // rotte del locale corrente
});
```

Questa logica rende semplice costruire URL coerenti tramite nome route e locale senza duplicare manualmente i path.

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
