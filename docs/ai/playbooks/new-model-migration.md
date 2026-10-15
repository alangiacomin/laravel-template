# Aggiungere un Model Eloquent e Migration

Questo playbook descrive la procedura operativa per creare una nuova entità/model Eloquent e la corrispondente migration all'interno della Clean Architecture modulare del progetto.

## Precondizioni

1. **Area e Contesto identificati**: prima di operare, l'agente deve conoscere con certezza l'Area (`Main`, `Admin`, ecc.) e il Contesto (`TodoList`, `Users`, ecc.). Se non specificati, **deve richiederli all'utente** prima di procedere.
2. **Nome del Model**: deve corrispondere al nome dell'entità specificato (es. `Todo`).

## Procedura

1. **Crea la Migration** (`php artisan make:migration create_{model_snake}_table --create={model_snake} --no-interaction`):
   - File: `database/migrations/<timestamp>_create_{model_snake}_table.php`
   - Tabella: al **singolare** in `snake_case` (es. `Schema::create('todo', ...)`).
   - Includi le colonne obbligatorie e opzionali richieste.
   - Non inserire `$table->softDeletes()` a meno di esplicita richiesta.
   - Assicurati che `down()` esegua `Schema::dropIfExists('{model_snake}');`.

2. **Crea la directory e la classe Model** (`php artisan make:model {Model} --no-interaction` crea in `app/Models`: **sposta il file** nel percorso sotto e correggi il namespace, oppure crealo direttamente lì):
   - Directory: `app/Areas/{Area}/{Context}/Infrastructure/Persistence/Eloquent/Models/`
   - File: `{Model}.php`
   - Namespace: `App\Areas\{Area}\{Context}\Infrastructure\Persistence\Eloquent\Models`
   - Proprietà tabella: definisci sempre esplicitamente `protected $table = '{model_snake}';`.
   - Proprietà `$fillable`: definisci i campi mass-assignable.
   - Metodo `casts()`: definisci il casting dei campi (es. booleani, date, enum).
   - Trait:
     - Non includere `HasFactory` a meno che l'utente non abbia richiesto espressamente la factory.
     - Non includere `SoftDeletes` a meno che l'utente non abbia richiesto espressamente il soft delete.

3. **Factory (opzionale, solo se richiesta)**:
   - Se richiesta una factory: crea `database/factories/{Model}Factory.php`.
   - Collega la factory nel model tramite il trait `HasFactory` e, se necessario, il metodo `newFactory()`.

## Verifica

```bash
vendor/bin/pint --dirty --format agent
composer lint                                 # Pint + PHPStan
php artisan test --compact --filter={Model}   # test che usano RefreshDatabase (esegue le migration)
```

Se il model alimenta una pagina, continua con [new-crud-job.md](new-crud-job.md) o [new-page.md](new-page.md).
