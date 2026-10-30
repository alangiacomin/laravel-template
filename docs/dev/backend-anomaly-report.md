# Report anomalie backend

Elenco delle anomalie rilevate nell'analisi del backend (`app/`, `routes/`, `bootstrap/`, `database/`), confrontato con `docs/ai/architecture.md` e `docs/ai/workflow.md`. Ogni punto ha un ID stabile (`#1`…`#22`) pensato per essere citato nei prompt, ad esempio: _"esegui il punto #2 di `docs/dev/backend-anomaly-report.md`"_.

## Istruzioni per l'agente

### Obiettivo

Questo file è il punto di partenza per fix **mirati, uno per volta**. Non è un piano da eseguire in blocco: l'utente indica di volta in volta quale punto (`#N`) risolvere.

### Come lavorare su un punto

1. Lavora **solo** sul punto indicato dall'utente (ID `#N`). Non toccare gli altri punti, nemmeno se sembrano collegati: segnalali nella risposta finale.
2. Prima di scrivere codice leggi:
   - `docs/ai/README.md`, `docs/ai/architecture.md` e `docs/ai/workflow.md` (inclusi chiarimenti bloccanti su Area e Contesto);
   - le linee guida del repository (`AGENTS.md`);
   - i file citati nel campo **Dove** del punto.
3. **Verifica sempre il punto nel codice prima di correggerlo.** L'analisi è statica e può contenere imprecisioni o essere superata da modifiche successive. Se il problema non è riproducibile o è già risolto, aggiorna lo **Stato** con una nota e fermati.
4. Se il punto richiede una **scelta di design** (es. posizione del model `User`, sistema di autorizzazione da adottare, rinomina route/frontend), **chiedi conferma all'utente prima di procedere**.
5. Segui le convenzioni esistenti: Aree/Contesti, Job in `Application/Jobs`, Request in `Presentation/Http/Requests`, controller sottili, route con attributi/middleware già in uso.

### Regole di chiusura di ogni fix

- Aggiungi o aggiorna i test utili a coprire il comportamento corretto e i casi di errore (non oltre).
- Esegui, nell'ordine:
  ```bash
  php artisan test --compact
  vendor/bin/phpstan analyse
  vendor/bin/pint --dirty --format agent
  ```
- Aggiorna lo **Stato** del punto in questo file: nell'indice `[ ]` → `[x]` e nel dettaglio la riga *Stato*, con una nota breve (cosa è stato fatto, eventuali decisioni, test aggiunti).
- Non rinumerare i punti: gli ID restano stabili anche quando un punto è chiuso o annullato.

### Cosa non è stato analizzato a fondo

Da non considerare coperto dall'analisi:

- Data class: `UserData`, `AdminUserData`, `Role`, `DashboardStats`;
- listener `Broadcast` e `ExamplePageController`;
- `config/*` e frontend `resources/js`;
- contenuto dei test;
- `RolesAndPermissionsSeeder`, `EditUserRequest`, `RegisterRequest`, `SessionPreserver`.

## Stato di partenza

- Data dell'analisi: 2026-10-29.
- `php artisan test`: 59 test passati.
- PHPStan: nessun errore, ma a **livello 5** (massimo 10), quindi poco rassicurante.

## Indice

Ordine di priorità consigliato: **P0** (gravità alta) → **P1** (gravità media) → **P2** (gravità bassa).

### P0 — Gravità alta

- [x] #1 `UserRegisteredEvent` senza listener (mail di verifica non inviata) `[alta]`
- [x] #2 IDOR su `PATCH /profilo/{id}` `[alta]`
- [x] #3 Logout via GET `[alta]`
- [x] #4 Nessun rate limiting sul login ed enumerazione account `[alta]`
- [x] #5 Seeder con super-admin prevedibile senza guardia sull'ambiente `[alta]`
- [x] #6 Blocco utenti senza guardie sul target `[alta]`
- [x] #7 Gestore eccezioni che espone dettagli al client `[alta]`

### P1 — Gravità media

- [x] #8 Sessione non rigenerata dopo registrazione; utente bannato autenticato prima del controllo `[media]`
- [x] #9 Flusso auth incompleto (verifica email, reset password) `[media]`
- [x] #10 Logica anti-escalation nel livello Presentation (`RoleUpdateRequest`/`UserUpdateRequest` Admin) `[media]`
- [x] #11 Doppio sistema di autorizzazione incoerente (Gate, Request, Policy) `[media]`
- [x] #12 `UserController::update` senza Job né risposta `[media]`
- [x] #13 `User::$fillable` include `banned_at`; seed non verificati `[media]`
- [x] #14 `RegisterUserJob` senza transazione `[media]`
- [x] #15 Middleware `not_banned` non uniforme `[media]`
- [x] #16 Helper di redirect con `redirect()->intended()` `[media]`

### P2 — Gravità bassa / manutenzione

- [x] #17 Performance (traduzioni Inertia, paginazione, query dashboard) `[bassa]`
- [x] #18 Routing (catch-all, `whereNumber`, gruppo admin) `[bassa]`
- [x] #19 Residui di codice `[bassa]`
- [x] #20 `BroadcastController` nel livello Presentation `[bassa]`
- [x] #21 Lingua mista IT/EN nel codice `[bassa]`
- [x] #22 Copertura test e livello PHPStan `[bassa]`

## Dettaglio dei punti

### P0 — Gravità alta

#### #1 `UserRegisteredEvent` senza listener

- **Dove**: `bootstrap/app.php` (`withEvents(discover: ['app/Main/*/Domain/Listeners'])`), `EventServiceProvider::$listen`, `app/Areas/Main/Auth/Infrastructure/Listeners/UserRegistered/` (`SendEmail` e listener Broadcast).
- **Problema**: il percorso di discovery punta a una cartella inesistente; i listener reali stanno in `app/Areas/Main/Auth/Infrastructure/Listeners/UserRegistered/` e `EventServiceProvider::$listen` è vuoto. `php artisan event:list --event=UserRegistered` non restituisce nulla. Inoltre `SendEmail` non invia nulla: ricarica l'utente e rilancia `Registered`.
- **Impatto**: dopo la registrazione la mail di verifica non parte e il broadcast è inattivo.
- **Fix suggerito**: puntare il discovery a `app/Areas/*/*/Infrastructure/Listeners` oppure registrare esplicitamente i listener in `EventServiceProvider::$listen`; far inviare davvero la verifica (listener standard su `Registered` / `sendEmailVerificationNotification`); aggiungere un test con `Event::fake` / `Notification::fake`. **Ri-verificare prima con `php artisan event:list`** (vedi note sui punti ambigui).
- **Stato**: [x] risolto: rimossi `UserRegisteredEvent`, listener custom, `VerifyEmailCustom` e override in `User`; `RegisterUserJob` lancia `Registered` e Laravel invia la `VerifyEmail` di default. Verificato: `event:list` senza residui, test `AuthControllerTest::test_a_user_can_register` (`Notification::assertSentTo($user, VerifyEmail::class)`), suite e phpstan verdi. Il broadcast sulla registrazione è stato rimosso: se serve ancora, decisione a parte.

#### #2 IDOR su `PATCH /profilo/{id}`

- **Dove**: `app/Areas/Main/Auth/Presentation/Http/Controllers/UserController.php` (`update`), `app/Areas/Main/Auth/Presentation/Http/Requests/UserUpdateRequest.php` (`authorize()` = `Auth::check()`).
- **Problema**: nessun controllo `id == Auth::id()`.
- **Impatto**: qualsiasi utente autenticato può cambiare il `name` di qualsiasi altro utente.
- **Fix suggerito**: usare l'utente autenticato (route `/profilo` senza `{id}`, aggiornando il frontend) oppure `abort_unless($id === Auth::id(), 403)`. Test dedicato sull'IDOR.
- **Stato**: [x] fatto — scelta (confermata dall'utente) route senza id: `PATCH /profilo` (nome `user.update` invariato) e `UserController::update` aggiorna sempre `$request->user()`; `EditProfileModal.tsx` chiama `route("user.update")` senza id. Test: i 4 test esistenti aggiornati alla nuova route e aggiunto `test_user_cannot_update_another_users_profile` (il vecchio URL `/profilo/{id}` non modifica nessun utente: cade nella catch-all). Restano fuori: Job e risposta/flash in `update` (vedi #12) e `whereNumber` sulle altre route (vedi #18).

#### #3 Logout via GET

- **Dove**: `routes/web_auth.php` (`/esci`), `AuthController::logout`, frontend che invoca il logout.
- **Problema**: il logout è raggiungibile via GET.
- **Impatto**: vulnerabile a logout forzato cross-site (CSRF) e a prefetch.
- **Fix suggerito**: passare a `POST` e aggiornare il frontend. Richiede conferma se cambia il nome/percorso della route.
- **Stato**: [x] fatto — `/esci` (nome `logout`, invariati) ora è `POST`; `Navbar` (via `NavbarDropdownItem`, nuovo prop `method`) e `Header` admin usano `<Link method="post" as="button">`. Test: logout via `POST` aggiornato e aggiunto `test_logout_is_not_reachable_via_get` (il GET su `/esci` non disconnette più: cade nella catch-all `/{any}`). Resta fuori: il logout non passa da `not_banned` (vedi #15).

#### #4 Nessun rate limiting sul login ed enumerazione account

- **Dove**: `POST /accedi` (`AuthController::login`), `app/Areas/Main/Auth/Presentation/Http/Requests/LoginRequest.php`, route di registrazione.
- **Problema**: nessun `throttle`; `LoginRequest` usa `exists:users,email`. `LoginRequest` è inoltre una `Spatie\Data`, non una FormRequest, quindi non passa dal flusso standard di validazione.
- **Impatto**: enumerazione degli account e brute force.
- **Fix suggerito**: `throttle` su login e registrazione (`RateLimiter`); sostituire `exists:users,email` con un messaggio generico di credenziali errate.
- **Stato**: [x] fatto — rimosso `exists:users,email` da `LoginRequest` (email sconosciuta e password errata danno lo stesso `auth.failed`); `AuthController::login` usa `RateLimiter` (5 tentativi falliti per email+IP, chiave case-insensitive, messaggio `auth.throttle`, contatore azzerato al login riuscito); `POST /registrati` ha `throttle:register` (10/minuto per IP, limiter definito in `AppServiceProvider`). Test aggiunti in `AuthControllerTest`. Restano fuori: `unique:users,email` in registrazione (mitigato solo dal throttle), il 429 che passa dall'handler di `bootstrap/app.php` (vedi #7) e l'ordine ban/`Auth::attempt` in `login` (vedi #8).

#### #5 Seeder con super-admin prevedibile

- **Dove**: `database/seeders/DatabaseSeeder.php`, `database/seeders/UsersSeeder.php`.
- **Problema**: creano `admin@example.com` con password `password123` e ruolo SUPER_ADMIN, senza guardia sull'ambiente.
- **Impatto**: se eseguiti in produzione = backdoor.
- **Fix suggerito**: proteggere con `app()->isLocal()` / `environment('local', 'testing')` oppure leggere le credenziali da `.env`.
- **Stato**: [x] fatto — scelta (confermata dall'utente) guardia sull'ambiente: `UsersSeeder` esce subito fuori da `local`/`testing`; `DatabaseSeeder` fuori da `local`/`testing` esegue solo `RolesAndPermissionsSeeder` (nessun utente demo né `assignRole`). Test: `DatabaseSeederTest` (testing e production). Resta fuori: `forceFill`/fillable del seeder (vedi #13).

#### #6 Blocco utenti senza guardie sul target

- **Dove**: `app/Areas/Admin/Users/Presentation/Http/Controllers/UserController.php` (`blocca`), `app/Areas/Admin/Users/Application/Jobs/BloccaUtenteJob.php`.
- **Problema**: nessun controllo sull'utente da bloccare.
- **Impatto**: un admin può bannare sé stesso, un super-admin o un altro admin.
- **Fix suggerito**: impedire auto-ban e ban di utenti con ruolo SUPER_ADMIN/ADMIN, nel Job o in una policy.
- **Stato**: [x] fatto — guardie nel Job (scelta confermata): `BloccaUtenteJob` riceve `actorId` e lancia `HttpException` 403 per auto-ban e target `SUPER_ADMIN`; gli admin restano bannabili (confermato). Messaggi in `lang/it/admin.php`. Test aggiunti in `UserControllerTest` (normale, altro admin, self, super-admin). Restano fuori #11, #12, #15.

#### #7 Gestore eccezioni che espone dettagli

- **Dove**: `bootstrap/app.php` (handler delle eccezioni, `renderable`).
- **Problema**: `$e->getMessage()` viene passato al client come `reason` anche per i 500 e flashato come `error` sulle richieste Inertia non-GET; `renderable` intercetta qualsiasi `Throwable`, usa `$e->getCode()` come status (può non essere un codice HTTP valido) e contiene `dd()` commentati.
- **Impatto**: fuga di informazioni interne e status code errati.
- **Fix suggerito**: non esporre `getMessage()` in produzione; limitare `renderable` ai `HttpException`; rimuovere i `dd` commentati.
- **Stato**: [x] fatto — `renderable` in `bootstrap/app.php` gestisce solo `HttpException` (status da `getStatusCode()`, `reason` vuoto per 5xx fuori debug), `dd()` rimossi; le eccezioni generiche usano il rendering di Laravel (nessun messaggio interno esposto). Test in `ExceptionHandlingTest`. Nota: i 500 non HTTP perdono l'ErrorPage Inertia.

### P1 — Gravità media

#### #8 Sessione non rigenerata dopo registrazione; utente bannato autenticato

- **Dove**: `app/Areas/Main/Auth/Presentation/Http/Controllers/AuthController.php` (`register`, `login`).
- **Problema**: `register` fa `Auth::login` senza `session()->regenerate()`; in `login`, `Auth::attempt` autentica anche l'utente bannato prima del controllo `isBanned()`.
- **Impatto**: rischio di session fixation; sessione autenticata temporanea per utenti bannati.
- **Fix suggerito**: rigenerare la sessione dopo la registrazione; controllare il ban prima di autenticare (o fare logout immediato).
- **Stato**: [x] fatto — `register` rigenera la sessione dopo `Auth::login`; `login` verifica le credenziali via user provider (`retrieveByCredentials` + `validateCredentials`), controlla `isBanned()` e solo dopo fa `Auth::login` + `session()->regenerate()`: il bannato non viene mai autenticato (l'errore `account_banned` compare solo con password corretta). Test: aggiunto `test_registration_regenerates_the_session`, `test_a_banned_user_cannot_login` già esistente. Restano fuori #9, #15, #16.

#### #9 Flusso auth incompleto

- **Dove**: `routes/web_auth.php`, `AuthController::emailVerification`, `password_reset_tokens`, `lang/it/passwords.php`.
- **Problema**: nessuna route per reinvio della mail di verifica né per il reset password, pur esistendo tabella e traduzioni. Il middleware `verified` non è usato: l'email verificata non limita nulla. `emailVerification` risponde con una pagina Inertia su GET invece di un redirect.
- **Impatto**: funzionalità attese ma mancanti; la verifica email non ha effetto.
- **Fix suggerito**: da decidere con l'utente (implementare reinvio/reset, applicare `verified` dove serve, redirect in `emailVerification`).
- **Stato**: [x] fatto — `emailVerification` fa redirect a `home` con flash di successo (rimossa `VerificationVerify.tsx`); aggiunta `verification.send` (POST, `auth`, `throttle:6,1`, `ResendVerificationEmailJob`) con pulsante in `VerificationNotice`; reset password con `PasswordResetController`, `SendPasswordResetLinkJob`, `ResetPasswordJob`, richieste dedicate, pagine `ForgotPassword`/`ResetPassword`, link in `Login` e risposta di invio sempre generica; `verified` applicato a `/profilo` (GET/PATCH) e a tutti i controller admin. Test: `AuthControllerTest`, `PasswordResetTest`, `VerifiedMiddlewareTest`.

#### #10 Logica anti-escalation nel livello Presentation

- **Dove**: `app/Areas/Admin/Roles/Presentation/Http/Requests/RoleUpdateRequest.php`, `app/Areas/Admin/Users/Presentation/Http/Requests/UserUpdateRequest.php`.
- **Problema**: la logica è corretta nelle intenzioni (blocca super-admin, ruoli posseduti, ruoli privilegiati) ma risiede in Presentation e contiene `User::find` nella `after()`. Resta un rischio indiretto: un non super-admin può assegnare qualsiasi permesso a un ruolo che non possiede.
- **Impatto**: regole di sicurezza difficili da testare e riusare; possibile escalation indiretta via permessi.
- **Fix suggerito**: spostare la logica in Application (Job) o in policy; chiudere il caso dei permessi assegnabili a un ruolo.
- **Stato**: [x] fatto — logica spostata in `UpdateRolePermissionsJob` (Admin/Roles) e `UpdateUserRolesJob` (Admin/Users); i rifiuti restano `ValidationException` su `permissions`/`roles` (scelta confermata). Le Request mantengono solo il controllo sulle chiavi note. Caso permessi chiuso: un non super-admin può cambiare a un ruolo solo permessi che possiede (super-admin senza limiti). `UserController::update` ora passa solo `name` a `update()`. Test aggiunti in `RoleControllerTest` (permesso non posseduto rifiutato, permesso posseduto consentito). Resta fuori #12 (Job per il nome e risposta/flash) e #11.

#### #11 Doppio sistema di autorizzazione incoerente

- **Dove**: `AppServiceProvider` (Gate), controller (`can:GateEnum::X`), Request (`PermissionEnum::X`), `app/Models/Policies/{User,Role}Policy`.
- **Problema**: controller → `can:GateEnum::X`; Request → `PermissionEnum::X` diretto; le policy non sono mai invocate (nessun `authorize()`/`Gate::policy`; `UserPolicy` è scoperta per convenzione, `RolePolicy` no perché il model è Spatie). `RolePolicy::update` usa `||` dove probabilmente serviva `&&` (`ROLE_UPDATE || name !== super-admin`). `GateEnum::USER_MANAGE` richiede 4 permessi (create/read/update/delete) per bannare: troppo rigido e poco leggibile. Il trait `AuthorizesRequests` è incluso ma inutilizzato.
- **Impatto**: regole di accesso duplicate e potenzialmente discordanti; policy morte.
- **Fix suggerito**: scegliere un solo meccanismo (consigliato: Gate in `AppServiceProvider` per le capability + Policy per le istanze, registrate con `Gate::policy`), rimuovere le duplicazioni nelle Request, correggere `RolePolicy::update` dopo aver verificato l'intento. **Richiede conferma dell'utente sul design.**
- **Stato**: [x] fatto — scelta (confermata) Gate + Policy: `Gate::policy` per `User` e `Spatie\Permission\Models\Role` in `AppServiceProvider`; i controller mantengono `can:GateEnum::X`; le tre `UserUpdateRequest`/`RoleUpdateRequest` delegano `authorize()` alla Policy (nessun `PermissionEnum` diretto). `RolePolicy::update` ora usa `&&`. `USER_MANAGE` invariato (scelta confermata). Effetto: un non super-admin che modifica il ruolo `super-admin` riceve 403 (prima 422 dal Job, che mantiene la guardia). Test: `test_non_super_admin_cannot_update_super_admin_role` ora 403, aggiunto `test_policies_are_registered_for_user_and_spatie_role`.

#### #12 `UserController::update` senza Job né risposta

- **Dove**: `app/Areas/Main/Auth/Presentation/Http/Controllers/UserController.php`, `app/Areas/Admin/Users/Presentation/Http/Controllers/UserController.php`.
- **Problema**: `update` non restituisce nulla né usa un Job, contro il pattern CRUD dei docs (`dispatch_sync(new XJob)` + risposta/flash). L'Admin fa `update($validatedData)` passando anche la chiave `roles`, che Eloquent scarta in silenzio.
- **Impatto**: controller non sottili, comportamento non uniforme, ruoli non aggiornati.
- **Fix suggerito**: creare Job (`UpdateUserJob`, `UpdateUserRolesJob`) con risposta/flash esplicita (`flashSuccess`).
- **Stato**: [x] fatto — nuovi `UpdateUserJob` (Admin/Users) e `UpdateProfileJob` (Main/Auth); entrambi i controller `update` usano `dispatch_sync` e rispondono con `flashSuccess` (`admin.user_updated`, `user.profile_updated`). Il problema della chiave `roles` scartata era già risolto da #10 (`UpdateUserRolesJob`). Test: i due test di update esistenti ora verificano redirect e flash `success`. Nota: `UpdateUserRolesJob` riceve `roles ?? []`, quindi un PATCH senza `roles` rimuove tutti i ruoli del target (comportamento preesistente, da valutare a parte).

#### #13 `User::$fillable` include `banned_at`; seed non verificati

- **Dove**: `app/Models/User.php`, `database/seeders/UsersSeeder.php`.
- **Problema**: `banned_at` è assegnabile in massa, ma il model ha già `ban()/unban()` con `forceFill`. `email_verified_at` e `remember_token` non sono fillable: `UsersSeeder::updateOrCreate` li scarta in silenzio.
- **Impatto**: rischio di mass assignment; utenti seed non verificati.
- **Fix suggerito**: togliere `banned_at` da `$fillable`; correggere il seeder con `forceFill` (aggiungere `email_verified_at` a `$fillable` solo se necessario).
- **Stato**: [x] fatto — `banned_at` rimosso da `User::$fillable` (restano `ban()/unban()`); `UsersSeeder` usa `firstOrNew` + `forceFill` + `save`, quindi `email_verified_at` e `remember_token` vengono salvati senza ampliare `$fillable`. Test in `DatabaseSeederTest`: utenti seed verificati e `banned_at` non assegnabile in massa.

#### #14 `RegisterUserJob` senza transazione

- **Dove**: `app/Areas/Main/Auth/Application/Jobs/RegisterUserJob.php`.
- **Problema**: `create` + `assignRole` + `event()` non sono atomici.
- **Impatto**: un errore su `assignRole` lascia un utente senza ruolo.
- **Fix suggerito**: racchiudere in transazione (evento dopo il commit).
- **Stato**: [x] fatto — `create` + `assignRole` in `DB::transaction` e `event(new Registered)` dopo il commit. Test: `test_registration_is_rolled_back_when_role_assignment_fails` (ruolo mancante → nessun utente creato, nessuna notifica). Restano fuori #15, #16.

#### #15 Middleware `not_banned` non uniforme

- **Dove**: controller con attributi middleware, `routes/web.php`, `FallbackController`, `ExamplePageController`.
- **Problema**: `not_banned` è applicato per singolo metodo; assente su `verificationNoticeView`, `logout`, route `example.page` e route Fallback; non è nel gruppo `web`.
- **Impatto**: dimenticare il middleware lascia route accessibili a utenti bannati.
- **Fix suggerito**: applicare `not_banned` (e `SessionPreserver`) a livello di gruppo `web`, valutando le eccezioni (logout, pagina di blocco).
- **Stato**: [x] fatto — `EnsureUserIsNotBanned` è ora nel gruppo `web` (`bootstrap/app.php`, prima di `HandleInertiaRequests`); rimossi l'alias `not_banned` e tutti gli attributi `#[Middleware('not_banned')]` dai controller. Unica eccezione: `POST /esci` (`withoutMiddleware`), così un bannato può sempre uscire. Coperte ora anche `example.page`, `verification.notice`, fallback e reset password. Test in `AuthControllerTest`: bannato bloccato su route prima scoperte e logout consentito. `SessionPreserver` non toccato (usato già dal middleware). Resta fuori #16.

#### #16 Helper di redirect con `redirect()->intended()`

- **Dove**: helper `spaRedirect` / `hardRedirect`.
- **Problema**: usano `redirect()->intended()`, che può sovrascrivere la destinazione richiesta (es. dopo logout o registrazione → `verification.notice`).
- **Impatto**: redirect inattesi.
- **Fix suggerito**: usare `intended()` solo dopo il login; negli altri casi redirect esplicito.
- **Stato**: [x] fatto — `spaRedirect`/`hardRedirect` fanno ora un redirect esplicito (`redirect($route)`); nuovo `hardIntendedRedirect` con `intended()` usato solo da `AuthController::login`. Test in `ControllerTest` (redirect che ignorano l'intended, intended rispettato dopo il login).

### P2 — Gravità bassa / manutenzione

#### #17 Performance

- **Dove**: `app/Infrastructure/Middleware/HandleInertiaRequests.php`, `Admin/Users/.../UserController::index`, `DashboardController`.
- **Problema**: `HandleInertiaRequests` carica con `glob` + `require` tutte le traduzioni di `lang/it` a ogni richiesta, senza cache né lazy; `UserData`/capabilities sono condivisi anche nelle richieste parziali. `index` fa `User::with('roles')->get()` senza paginazione. `DashboardController` esegue 6 `count()` separate e contiene logica di date.
- **Impatto**: sprechi di risorse che crescono con i dati.
- **Fix suggerito**: cache/lazy delle traduzioni; paginazione utenti/ruoli; riunire le query della dashboard (spostando la logica in un Job/query dedicata).
- **Stato**: [x] fatto — traduzioni Inertia in cache (chiave con mtime dei file di `lang/it`) e `translations`/`auth` lazy (closure, valutate solo quando servono); lista utenti admin paginata (`paginate(15)`, controlli pagina in `Users.tsx`, chiave `admin.pagination`); statistiche dashboard in `GetDashboardStatsQuery` con una sola query aggregata. Test: paginazione, statistiche dashboard, traduzioni su richieste ripetute. Ruoli non paginati (pochi). Non verificato: `bun run lint/build` (bun/node assenti nell'ambiente).

#### #18 Routing

- **Dove**: `routes/web.php`, `routes/web_auth.php`, `routes/web_admin.php`, `FallbackController`.
- **Problema**: `/` è mappata su `FallbackController::app` (nome fuorviante); la catch-all `/{any}` e `/admin/{any}` copre un solo segmento; route PATCH `/profilo/{id}` e `/admin/.../{id}` senza `whereNumber`; `require` dei file route in mezzo a `web.php`; nessun gruppo con middleware/prefix per l'area admin (la protezione è solo negli attributi dei controller: dimenticarne uno = route pubblica).
- **Impatto**: fragilità e rischio di esposizione accidentale.
- **Fix suggerito**: `whereNumber('id')`; raggruppare le route admin con `Route::middleware(['auth', 'not_banned', 'can:admin.access'])->prefix('admin')`; sistemare nomi e catch-all.
- **Stato**: [x] fatto — route admin in gruppo `prefix('admin')` con `auth`, `verified`, `can:admin_access` (i `can:` per capability restano sui controller); `whereNumber('id')` su admin e `verification.verify`; catch-all `/admin/{any}` multi-segmento e `Route::fallback` per il resto; `/` ora `HomeController::index` (nuovo, `Main/Home`), `FallbackController` solo `notFound`. La catch-all admin resta fuori dal gruppo (un ospite su `/admin/x` ottiene 404, non il login). Test in `RoutingTest` e `UserControllerTest`.

#### #19 Residui di codice

- **Dove**: `AppServiceProvider`, `EventServiceProvider`, `bootstrap/app.php`, `app/Models/User.php`.
- **Problema**: `Queue::before` con `Context::get('locale')` (app a lingua unica), blocchi `DB::beginTransaction` commentati e `JsonResource::withoutWrapping` in `AppServiceProvider`; `use Event;` e `$listen` vuoto in `EventServiceProvider`; `/** @noinspection */` e `dd` commentati; docblock `@method static ...` su `User` (incl. `register()` inesistente) usato come workaround di PHPStan.
- **Impatto**: rumore e confusione.
- **Fix suggerito**: pulizia dei residui; sostituire il docblock con annotazioni corrette.
- **Stato**: [x] fatto — rimossi `Queue::before`/`Context`, blocchi `DB::beginTransaction` e `withoutWrapping` commentati, `EventServiceProvider` (inutilizzato) e il riferimento in `bootstrap/providers.php`, `prepend: []`, `@noinspection`; docblock `@method` di `User` sostituito da `@use HasFactory<UserFactory>`.

#### #20 `BroadcastController` nel livello Presentation

- **Dove**: `BroadcastController`, `routes/channels.php`.
- **Problema**: è un authorizer di canale (`join`) collocato tra i controller HTTP di `Presentation`.
- **Impatto**: collocazione architetturale incoerente.
- **Fix suggerito**: spostare in `Infrastructure/Broadcasting` aggiornando `routes/channels.php`.
- **Stato**: [x] fatto — `BroadcastController` sostituito da `Main/Auth/Infrastructure/Broadcasting/UserChannel` (tipizzato), `routes/channels.php` aggiornato. Test in `RoutingTest`.

#### #21 Lingua mista IT/EN nel codice

- **Dove**: Job, controller e route (`BloccaUtenteJob`, `blocca`, `/accedi` vs `UserController`, `register`).
- **Problema**: nomi italiani e inglesi mescolati.
- **Impatto**: convenzioni non uniformi.
- **Fix suggerito**: decidere con l'utente la convenzione e rinominare di conseguenza. **Richiede conferma.**
- **Stato**: [x] fatto — convenzione (confermata): identificatori in EN, URL e testi in IT. `BloccaUtenteJob`→`BlockUserJob`, `SbloccaUtenteJob`→`UnblockUserJob`, `blocca/sblocca`→`block/unblock`, route `admin.user.block/unblock` (URL invariati); frontend, ziggy e test aggiornati; regola scritta in `docs/ai/architecture.md`.

#### #22 Copertura test e livello PHPStan

- **Dove**: `tests/`, `phpstan.neon`.
- **Problema**: i test non coprono registrazione + evento, IDOR del profilo, ban di sé stessi, rate limit, enumerazione login; PHPStan è a livello 5.
- **Impatto**: i punti P0 possono regredire senza che nessun test fallisca.
- **Fix suggerito**: aggiungere test su registrazione + eventi, IDOR profilo, guardie sul ban, throttle login, escalation ruoli (preferibilmente insieme ai singoli fix); alzare PHPStan a livello 6–8 con baseline e limitare i `paths` a `app`, `bootstrap`, `config`, `database`, `routes`, `tests`.
- **Stato**: [x] fatto — PHPStan a livello 6 (nessun errore, senza baseline né ignore) con `paths` ridotti a app, bootstrap, config, database, routes, tests; corretti return type dei controller e array shape di `rules()`, Data e `AbilityResolver`. La copertura test dei punti P0 esisteva già; aggiunti i test dei nuovi comportamenti (#17–#20). Sistemato anche `DatabaseSeederTest::test_banned_at_is_not_mass_assignable`, che usava `forceFill` e non poteva passare (ora `fill`).

## Verifica post-fix (2026-10-30)

Verifica incrociata dei punti #1–#22 sui punti di contatto. Tutti i punti risultano presenti nel codice; corretto quanto segue (test: 117 passati, PHPStan livello 6 senza errori).

- **#7/#4**: il flash di errore sulle richieste Inertia non-GET non espone più `getMessage()` per 404 e 5xx (fuori debug) e traduce 403 generico e 429 (`error.429_message`); testo scritto dall'applicazione invariato. `isAdmin` usa `is('admin', 'admin/*')`.
- **#10/#12**: `UserController::update` (Admin) invoca `UpdateUserRolesJob` solo se `roles` è presente: un PATCH con solo `name` non cancella più i ruoli.
- **#6/#11**: `UserPolicy::update` nega a un non super-admin la modifica di un super-admin (il proprio profilo resta modificabile).
- **#18**: rimossi dai controller admin i middleware `auth`/`verified`/`can:admin_access` duplicati del gruppo in `routes/web_admin.php`.
- **#1**: rimosso il listener frontend `utente.registrato` (evento non più emesso); rigenerati `ziggy.js`/`ziggy.d.ts`.
- **#9**: reset password per utenti bannati confermato come scelta (già coperto da `test_banned_user_can_reset_password_but_cannot_login`).
- **Docs**: `docs/ai/workflow.md` allineato alla lingua unica, `docs/ai/architecture.md` a PHP 8.5.
- Non verificati: lint/build frontend (`bun` assente).

## Divergenze docs ↔ codice

Tutte le divergenze sono chiuse (2026-10-30).

| Docs | Realtà | Stato |
|---|---|---|
| Model in `Areas/.../Infrastructure/Persistence/Eloquent/Models`, tabella singolare | `app/Models/User.php`, tabella `users`, policy in `app/Models/Policies` | [x] `architecture.md`, `workflow.md` e `README.md` dichiarano `App\Models\User` come eccezione (nessuna migrazione del model) |
| Scritture via Job, controller sottili | `update` scriveva direttamente, logica nelle Request | [x] codice allineato con #10 e #12; nessuna modifica ai docs |
| Stack "PHP 8.4" | PHP 8.5 | [x] `architecture.md` aggiornato |
| `workflow.md`: lingue it/en e route localizzate | App a lingua unica IT | [x] `workflow.md` uniformato |
| Domain = logica di dominio | `Domain/Entities` conteneva Data per l'UI | [x] `Role` e `DashboardStats` spostati in `Application/Data`; `architecture.md` ha la sezione "Livelli" (`Domain` riservato a logica pura, oggi non usato) |
| Autorizzazione non documentata | Spatie + Gate + Policy | [x] documentata in `architecture.md` (Flussi applicativi CRUD) |

## Cose positive

Da preservare durante i fix:

- struttura Aree/Contesti coerente per i controller;
- Request per la validazione in Admin;
- middleware `auth` / `not_banned` / `can:` dichiarati con attributi;
- `Gate::before` blocca i bannati;
- `EnsureUserIsNotBanned` invalida la sessione;
- test e PHPStan verdi;
- `UserUpdateRequest` Admin ha già una difesa anti-escalation.

## Note sui punti ambigui

Tutti verificati e chiusi (2026-10-30), nessuna modifica al codice necessaria:

- **#1**: `php artisan event:list` mostra solo `Registered` → `SendEmailVerificationNotification`; nessun residuo di `UserRegistered`, `withEvents` o `EventServiceProvider`.
- **#11 — `RolePolicy::update`**: usa già `&&` (`ROLE_UPDATE && name !== super-admin`), intento confermato.
- **#11 / #10**: meccanismo scelto Gate + Policy (`Gate::policy` in `AppServiceProvider`); le Request delegano a `can('update', ...)`.
- **Model `User`** (divergenze docs ↔ codice): deciso di documentare l'eccezione in `docs/ai`, senza migrare il model.
