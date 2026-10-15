# Aggiungere una scrittura (CRUD) con Job

Modello reale: `blocca` in `app/Areas/Admin/Users` (route → controller → job → flash).

## Precondizioni
Area, Contesto, nome dell'azione e permesso richiesto (`GateEnum`) confermati.

## Procedura (esempio: `Archivia` su `Report`)

1. **Route** (`routes/web*.php`): `Route::patch('/report.archivia/{id}', [ReportController::class, 'archivia'])->name('report.archivia');`
2. **Request** (se ci sono input) in `Presentation/Http/Requests/ReportArchiviaRequest.php`: `authorize()` e `rules()`; modello `UserUpdateRequest`.
3. **Job** in `Application/Jobs/ArchiviaReportJob.php`:
   ```php
   class ArchiviaReportJob
   {
       use Dispatchable;

       public function __construct(public int $id) {}

       public function handle(): void
       {
           Report::findOrFail($this->id)->update(['archiviato' => true]);
       }
   }
   ```
   Il `Report` è il model sotto `.../Infrastructure/Persistence/Eloquent/Models/` (vedi [new-model-migration.md](new-model-migration.md)).
4. **Controller**:
   ```php
   #[Middleware('can:'.GateEnum::REPORT_MANAGE->value)]
   public function archivia(int $id, ReportArchiviaRequest $request)
   {
       dispatch_sync(new ArchiviaReportJob($id));

       return $this->flashSuccess(__('report.archiviato'));
   }
   ```
   Se serve un nuovo gate: aggiungilo a `GateEnum`/`PermissionEnum` (`app/Shared/Infrastructure/Enums`), al seeder dei ruoli e rigenera i tipi.
5. **Traduzioni**: `lang/it/report.php` → `'archiviato' => '...'`.
6. **Frontend**: `inertiaRouter.patch(route("report.archivia", { id }), {}, { onSuccess })` o `form.patch(...)`, con toast dal flash (esempio `onBlocca` in `Pages/Admin/Users/User.tsx`).
7. **Test**: caso felice (DB aggiornato + flash), non autorizzato (403), input non valido (se c'è Request); `RefreshDatabase` e seeder dei ruoli come in `UserControllerTest`.

## Verifica
```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact --filter=Report
php artisan typescript:transform   # solo se hai toccato Data/Enum
bun run check                      # solo se hai toccato il frontend
```
