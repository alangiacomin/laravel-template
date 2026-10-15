# Architettura e invarianti

## Stack

- Backend: Laravel 13 e PHP 8.4.
- Frontend: Inertia, React, TypeScript e Vite.
- Test: PHPUnit tramite `composer test` (`tests/Feature`, `tests/Unit`); frontend Vitest + Testing Library (`bun run test`, file `*.test.ts(x)` accanto al codice).
- Qualità PHP: Laravel Pint e PHPStan tramite `composer lint`.
- Qualità frontend: Biome tramite `bun run lint` (`bun run lint:fix` per le correzioni); `bun run check` = typecheck + lint + test.
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

- `app/Shared/Infrastructure/Enums`: `GateEnum`, `PermissionEnum`, `RoleEnum` (usati per autorizzazioni).
- `app/Infrastructure/Http/Controllers/Controller.php`: controller base.
- `app/Infrastructure/Middleware/HandleInertiaRequests.php`: shared props Inertia.
- **Eccezione legacy**: `app/Models/User.php` e `app/Models/Policies` esistono per auth/permessi (Spatie). **Non replicare**: ogni nuovo model va sotto `app/Areas/...` (vedi sotto).

### Struttura di un Contesto

Riferimento reale: `app/Areas/Admin/Users`. Crea solo le cartelle che servono.

```
app/Areas/{Area}/{Contesto}/
├── Presentation/Http/
│   ├── Controllers/{Risorsa}Controller.php   # estende App\Infrastructure\Http\Controllers\Controller
│   └── Requests/{Risorsa}{Azione}Request.php # FormRequest: validazione e authorize
├── Application/
│   ├── Jobs/{Verbo}{Oggetto}Job.php          # scritture: Dispatchable, handle()
│   └── Data/{Nome}Data.php                   # Spatie Data, #[TypeScript] se va al frontend
├── Domain/                                   # solo se c'è logica di dominio pura
└── Infrastructure/Persistence/Eloquent/Models/{Model}.php
```

Namespace = percorso (`App\Areas\Admin\Users\Application\Jobs`). Nomi in italiano
solo dove già presente nel contesto (es. `BloccaUtenteJob`); per nuovi contesti
segui la lingua dei file vicini.

### Regole rapide

- DO: controller sottile → Request valida → Job muta → redirect con flash.
- DO: autorizzazione sul metodo con `#[Middleware('can:'.GateEnum::X->value)]`; `#[Middleware('auth')]` sulla classe se l'area lo richiede.
- DO: `Model::findOrFail($id)` per ricerche da route.
- DON'T: model in `app/Models`, repository o action class per CRUD semplice, path/URL scritti a mano, prefissi di locale, query complesse inline nel controller.
- DON'T: aggiungere `HasFactory`, `SoftDeletes`, factory o seeder se non richiesti.

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

## Flussi CRUD

Schema per ogni scrittura (esempio reale: `blocca` in `app/Areas/Admin/Users`):

1. Route `PATCH|POST|DELETE` in `routes/web*.php` con nome logico.
2. Controller: validazione via `FormRequest`, poi `dispatch_sync(new XJob(...))`, poi `return $this->flashSuccess(__('chiave'));` (redirect `back()` + flash `success`). Altri helper del controller base: `spaRedirect($route)`, `hardRedirect($route)`.
3. Job in `Application/Jobs/*Job.php`: classe con `use Dispatchable;`, dati nel costruttore (`public int $id`), mutazione Eloquent in `handle(): void`. Nessuna action, nessun repository.
4. Letture: query Eloquent nel controller; se diventano lunghe, in una classe dedicata in `Application/`.
5. Dati verso il frontend: classe Spatie Data (`Application/Data`) con `#[TypeScript]` e factory statica `fromModel()`; non passare model Eloquent grezzi alla pagina.
6. `php artisan typescript:transform` rigenera `resources/js/types/generated/index.ts`; importa i tipi da lì, non ridefinirli.

Nota: `UserController::update` muta inline (legacy). Per nuovo codice usa il Job.

Playbook: [new-crud-job.md](playbooks/new-crud-job.md).

## Routing

Lingua unica italiana (`APP_LOCALE`/`APP_FALLBACK_LOCALE` in `.env` → `config/app.php`). **Nessun prefisso di locale nelle URL**, nessun `config/routes.php`, nessun `localizedRoute()`.

| File | Contenuto |
| --- | --- |
| `routes/web.php` | Area `Main` pubblica; richiede gli altri due file; contiene il fallback `/{any}` (deve restare **ultima**) |
| `routes/web_auth.php` | Login, registrazione, logout |
| `routes/web_admin.php` | Area `Admin`; il fallback `/admin/{any}` resta ultimo |

Convenzione reale: path e nome coincidono, con punti come separatori: `Route::get('/admin.users', ...)->name('admin.users')`, `/admin.user.show/{id}` → `admin.user.show`, `/example.page` → `example.page`. Schema nome: `{area}.{risorsa}.{azione}` (per `Main` senza prefisso area).

- Nel PHP usa `route('nome')` / `to_route('nome')`; nei test `route('nome')`.
- Nel frontend usa `route('nome')` di Ziggy, raccolto in `resources/js/hooks/useRoutes.ts`: **ogni nuova route navigabile va aggiunta lì**. `bun run ziggy:generate` aggiorna `resources/js/ziggy.js` (parte automaticamente in `dev` e `build`).
- Non scrivere path a mano né inserire prefissi di locale.

## Traduzioni

Unico set: `lang/it/*.php`, un file per contesto/pagina (es. `lang/it/example_page.php`). Chiave = `file.chiave` (`__('admin.user_blocked')` in PHP, `__("example_page.title")` in React tramite `useTranslations`). Chiave senza prefisso = file `global.php`. Ogni testo visibile all'utente e ogni messaggio di validazione passa da qui; vedi [frontend.md](frontend.md).

## Database e configurazione

Le modifiche allo schema richiedono una migration. Non modificare direttamente
il database SQLite locale come sostituto di una migration. Le impostazioni
sensibili appartengono a `.env` e non vanno aggiunte al repository.
