# Aggiungere un Model Eloquent e Migration

Questo playbook descrive la procedura operativa per creare una nuova entità/model Eloquent e la corrispondente migration all'interno della Clean Architecture modulare del progetto.

## Precondizioni

1. **Area e Contesto identificati**: prima di operare, l'agente deve conoscere con certezza l'Area (`Main`, `Admin`, ecc.) e il Contesto (`TodoList`, `Fumetti`, ecc.). Se non specificati, **deve richiederli all'utente** prima di procedere.
2. **Nome del Model**: deve corrispondere al nome dell'entità specificato (es. `Todo`).

## Procedura

1. **Crea la Migration**:
   - File: `database/migrations/<timestamp>_create_{model_snake}_table.php`
   - Tabella: al **singolare** in `snake_case` (es. `Schema::create('todo', ...)`).
   - Includi le colonne obbligatorie e opzionali richieste.
   - Non inserire `$table->softDeletes()` a meno di esplicita richiesta.
   - Assicurati che `down()` esegua `Schema::dropIfExists('{model_snake}');`.

2. **Crea la directory e la classe Model**:
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

1. Esegui la formattazione e l'analisi statica:
   ```bash
   composer lint
   ```
2. Esegui la suite di test pertinente:
   ```bash
   composer test
   ```
