# Aggiungere una nuova pagina

## Precondizioni
- Area e Contesto confermati (vedi [workflow.md](../workflow.md)).
- Noti: nome route (`{area}.{risorsa}.{azione}`, es. `admin.report.index` oppure `example.page`), componente `Pages/{App|Admin}/{Contesto}/{Pagina}.tsx`, permesso richiesto (se admin).

## Procedura (esempio: pagina pubblica `Report` in `Main/Report`)

1. **Route** nel file corretto (`routes/web.php` per Main, `routes/web_admin.php` per Admin), **prima** del fallback `/{any}`:
   ```php
   Route::get('/report.index', [ReportController::class, 'index'])->name('report.index');
   ```
   Path e nome coincidono, con punti come separatori.
2. **Controller** in `app/Areas/Main/Report/Presentation/Http/Controllers/ReportController.php`, estende `App\Infrastructure\Http\Controllers\Controller`:
   ```php
   public function index(): Response|ResponseFactory
   {
       return inertia('App/Report/Report');
   }
   ```
   Area Admin: aggiungi `#[Middleware('auth')]`, `#[Middleware('not_banned')]` alla classe e `#[Middleware('can:'.GateEnum::X->value)]` al metodo (modello: `UserController`). Dati per la pagina: classe Spatie Data con `#[TypeScript]` + `php artisan typescript:transform`.
3. **Pagina** `resources/js/Pages/App/Report/Report.tsx` (Admin: `Pages/Admin/Report/Report.tsx`) copiando `Pages/App/ExamplePage/ExamplePage.tsx`; tutti i testi con `__("report.chiave")`.
4. **Traduzioni**: crea `lang/it/report.php` (`'browser_title'`, `'title'`, ...).
5. **`useRoutes.ts`**: aggiungi la voce in `routes.app` / `routes.admin` (`report: () => route("report.index")`) se la pagina è linkata dalla UI; aggiungi il link nella navbar/layout dell'area se deve comparire nel menu.
6. **Test** in `tests/Feature/...` sul modello di `tests/Feature/Admin/UserControllerTest.php`: guest → redirect, utente senza permesso → 403, utente autorizzato → 200 (`route('report.index')`).

## Verifica
```bash
vendor/bin/pint --dirty --format agent
php artisan route:list --name=report
php artisan test --compact --filter=Report
bun run check
```
Aggiorna `docs/development.md` solo se introduci una nuova convenzione.
