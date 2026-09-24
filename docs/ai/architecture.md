# Architettura e invarianti

## Stack

- Backend: Laravel 13 e PHP 8.4.
- Frontend: Inertia, React, TypeScript e Vite.
- Test: PHPUnit tramite `composer test`.
- Qualità PHP: Laravel Pint e PHPStan tramite `composer lint`.
- Realtime: Laravel Reverb.

## Organizzazione del backend

Il codice applicativo vive in `app/`. Le funzionalità sono raggruppate per
area, per esempio:

- `app/Areas/Main`: funzionalità pubbliche e autenticazione.
- `app/Areas/Admin`: funzionalità amministrative.
- `app/Infrastructure`: servizi e integrazioni trasversali.
- `app/Shared`: componenti condivisi.

Quando una funzionalità appartiene chiaramente a un'area, non collocarla in
un'altra solo per comodità. Mantieni, dove la funzionalità lo richiede, la
separazione tra `Presentation`, `Application`, `Domain` e `Infrastructure`.
Controller e route devono coordinare la richiesta; la logica non banale va
spostata nel livello appropriato.

## Routing localizzato

I locali supportati sono definiti in `config/app.php` e attualmente sono `it` ed
`en`. I segmenti traducibili delle URL sono definiti in
`config/routes.php`. Le route web vengono registrate dentro il gruppo con
prefisso locale in `routes/web.php` o nel file specializzato corretto.

Per una nuova pagina:

- aggiungi la stessa chiave logica a tutti i locali;
- usa il valore configurato per costruire il path;
- assegna il nome route logico nel gruppo locale;
- considera che il nome effettivo include il prefisso (`it.`, `en.`);
- verifica sia URL sia route generate.

Non inserire path localizzati hardcoded in controller o componenti.

## Frontend e traduzioni

Le pagine Inertia devono seguire la struttura già presente in `resources/`.
Prima di creare un componente cerca componenti e tipi riutilizzabili.
Qualsiasi testo visibile all'utente deve passare dal sistema di traduzione
appropriato e avere le chiavi per `it` ed `en`, quando applicabile.

## Database e configurazione

Le modifiche allo schema richiedono una migration. Non modificare direttamente
il database SQLite locale come sostituto di una migration. Le impostazioni
sensibili appartengono a `.env` e non vanno aggiunte al repository.
