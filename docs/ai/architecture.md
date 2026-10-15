# Architettura e invarianti

## Stack

- Backend: Laravel 13 e PHP 8.4.
- Frontend: Inertia, React, TypeScript e Vite.
- Test: PHPUnit tramite `composer test`.
- Qualità PHP: Laravel Pint e PHPStan tramite `composer lint`.
- Qualità frontend: Biome tramite `bun run lint` (`bun run lint:fix` per le correzioni).
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

### Model Eloquent, Migration e Persistenza (Clean Architecture parziale)

I model Eloquent e le relative migration devono seguire la struttura modulare clean per area e contesto:

- **Percorso del Model**: `app/Areas/{Area}/{Context}/Infrastructure/Persistence/Eloquent/Models/{Model}.php` (es. `app/Areas/Main/TodoList/Infrastructure/Persistence/Eloquent/Models/Todo.php`).
- **Nome della classe Model**: deve corrispondere esattamente a quanto indicato nel prompt (es. `Todo`).
- **Nome della tabella**: deve rispettare lo stesso nome del model **senza pluralizzazione**, in `snake_case` (es. tabella `todo`), esplicitato obbligatoriamente nel model tramite la proprietà `protected $table = '...';`.
- **Convenzione Migration**:
  - File: `database/migrations/<timestamp>_create_{model_snake}_table.php` (es. `database/migrations/2026_09_29_000000_create_todo_table.php`).
  - Tabella creata: `Schema::create('{model_snake}', function (Blueprint $table) { ... });`.
  - Rollback: `Schema::dropIfExists('{model_snake}');`.
- **Trait `HasFactory`**: il trait `HasFactory` (e la relativa Factory in `database/factories/`) va inserito **solo se espressamente richiesto**.
- **Soft Deletes**: il trait `SoftDeletes` (e la colonna `$table->softDeletes()` nella migration) va inserito **solo se espressamente richiesto**.
- **Adeguamento successivo**: se le relative opzioni (Factory o Soft Delete) vengono introdotte in un momento successivo, il model e la migration devono essere adeguati di conseguenza.

#### Esempio completo: Creazione entità `Todo` in Area `Main`, Contesto `TodoList`

**Model**: app/Areas/Main/TodoList/Infrastructure/Persistence/Eloquent/Models/Todo.php
```php
<?php

namespace App\Areas\Main\TodoList\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $table = 'todo';

    protected $fillable = [
        'titolo',
        'completato',
    ];

    protected function casts(): array
    {
        return [
            'completato' => 'boolean',
        ];
    }
}
```

**Migration**: `database/migrations/2026_09_29_000000_create_todo_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todo', function (Blueprint $table): void {
            $table->id();
            $table->string('titolo');
            $table->boolean('completato')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo');
    }
};
```

---

### Flussi applicativi CRUD

- I controller validano tramite Request, invocano i job per le scritture (`dispatch_sync(new XJob(...))`) e compongono la risposta HTTP/Inertia;
- I job applicativi (`Application/Jobs/*Job`, classi con `Dispatchable`, creati senza queue) contengono le mutazioni usando Eloquent, senza delegarle ad action e senza introdurre repository quando non servono;
- Le letture possono usare query Eloquent native, mantenute in classi applicative dedicate quando la loro composizione appesantisce il controller;
- I controller estendono `App\Infrastructure\Http\Controllers\Controller` (helper `flashSuccess`, `spaRedirect`, `hardRedirect`); l'autorizzazione tramite Gate si dichiara con `#[Middleware('can:'.GateEnum::X->value)]`;
- Usa Spatie Data per contratti espliciti di input/output condivisi o trasformati, annotandoli con `#[TypeScript]` quando devono generare tipi frontend. Le regole di validazione HTTP restano nei Request;
- Rigenera `resources/js/types/generated/index.ts` con `php artisan typescript:transform` e importa i tipi generati nei componenti, invece di ridefinire localmente le stesse strutture.

## Routing

L'applicazione è a lingua unica: italiano (IT), con fallback anch'esso IT,
determinati da `.env` (`APP_LOCALE`, `APP_FALLBACK_LOCALE`) in
`config/app.php`. Non esiste alcun prefisso di locale nelle URL né alcuna
gestione di sessione dedicata alla lingua.

Le route web vengono registrate direttamente, senza gruppo locale, nei file
`routes/web.php`, `routes/web_auth.php` e `routes/web_admin.php`.

Per una nuova pagina:

- aggiungi la route nel file corretto per area / ambito;
- assegna il nome route logico direttamente;
- usa `route()` / `to_route()` per costruire URL e link;
- non inserire path hardcoded o prefissi di locale in controller o componenti.

L'unico set di traduzioni presenti nell'applicazione è `lang/it/`.

## Frontend e traduzioni

Le pagine Inertia devono seguire la struttura già presente in `resources/`.
Prima di creare un componente cerca componenti e tipi riutilizzabili.
Qualsiasi testo visibile all'utente deve passare dal sistema di traduzione
appropriato, con le chiavi definite in `lang/it/`, l'unico set di traduzioni
presente nell'applicazione.

## Database e configurazione

Le modifiche allo schema richiedono una migration. Non modificare direttamente
il database SQLite locale come sostituto di una migration. Le impostazioni
sensibili appartengono a `.env` e non vanno aggiunte al repository.
