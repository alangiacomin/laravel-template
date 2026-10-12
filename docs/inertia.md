# Guida Tecnica a Inertia.js

Questo documento raccoglie le istruzioni tecniche, le convenzioni e le buone prassi per lo sviluppo con **Inertia.js** all'interno dell'applicazione (Laravel 13, React, TypeScript).

---

## 1. Architettura Generale e Bootstrap

Inertia funge da collante (*monolith moderno*) tra il backend Laravel e il frontend React, eliminando la necessità di creare API REST separate o gestire manualmente client HTTP per la navigazione e i form.

### Entry Point del Frontend (`resources/js/index.tsx`)

Il bootstrap dell'applicazione è configurato in [`resources/js/index.tsx`](file:///home/alan/Git/laravel-template/resources/js/index.tsx):

- **Risoluzione dinamica delle pagine**: utilizza `import.meta.glob('./Pages/**/*.tsx', { eager: true })`.
- **Layout automatico e persistente**: le pagine assegnano il rispettivo layout in modo automatico in base alla directory:
  - Pagine che iniziano con `Admin/` vengono incapsulate in `<AdminLayout />`.
  - Le altre pagine vengono incapsulate in `<AppLayout />`.
  - Non occorre importare o dichiarare manualmente il layout principale in ogni singola pagina; le pagine utilizzano solitamente il componente `<Page>` per definire titolo browser, header, breadcrumb e contenitore.

### Sincronizzazione dei Tipi (Backend ↔ Frontend)

Per garantire la type-safety:
1. I contratti dati su Laravel estendono `Spatie\LaravelData\Data` e sono annotati con l'attributo `#[TypeScript]`.
2. Il comando `php artisan typescript:transform` rigenera il file dei tipi frontend [`resources/js/types/generated/index.ts`](file:///home/alan/Git/laravel-template/resources/js/types/generated/index.ts).
3. I componenti React importano direttamente questi tipi senza ridefinire interfacce duplicate.

---

## 2. Frontend: Prelevare e Consumare i Dati

In questo progetto i dati arrivano al frontend attraverso due canali: **Page Props** (specifiche della singola pagina) e **Shared Props** (condivise globalmente).

### A. Page Props con `usePage`

Ogni controller backend invia un payload specifico alla pagina Inertia. Nel frontend si recuperano tipizzandoli tramite il generico di `usePage<T>()`.

Esempio pratico da [`resources/js/Pages/Admin/Fumetti/Testata.tsx`](file:///home/alan/Git/laravel-template/resources/js/Pages/Admin/Fumetti/Testata.tsx):

```tsx
import { FC, ReactNode } from "react";
import { usePage } from "@inertiajs/react";
import type { TestataDetailData } from "../../../types/generated";

type Props = {
    testata: TestataDetailData;
};

const Testata: FC = (): ReactNode => {
    // Estrazione delle props inviate dal controller
    const { testata } = usePage<Props>().props;

    return <div>{testata.titolo}</div>;
};

export default Testata;
```

> [!TIP]
> Tipizza sempre `usePage<Props>()` con i tipi generati da Spatie Data per avere autocompletamento e controllo del compilatore TypeScript su tutti i dati della pagina.

### B. Shared Props e Custom Hooks

I dati globali sono registrati nel middleware [`app/Infrastructure/Middleware/HandleInertiaRequests.php`](file:///home/alan/Git/laravel-template/app/Infrastructure/Middleware/HandleInertiaRequests.php) e sono accessibili ovunque. Per comodità e coerenza architetturale, vengono consumati tramite appositi custom hook:

1. **Autenticazione e Permessi (`useAuth`)**:
   - Middleware backend: condivide `auth.user` (`UserData`) e `auth.capabilities` (array di stringhe derivate da Gate e Policy).
   - Hook frontend: [`useAuth()`](file:///home/alan/Git/laravel-template/resources/js/hooks/useAuth.tsx) restituisce l'utente autenticato esteso con il metodo helper `.can(capability: string)`:
     ```tsx
     const currentUser = useAuth();
     const canEdit = currentUser && currentUser.can(GateEnum.USER_EDIT);
     ```

2. **Traduzioni e Localizzazione (`useTranslations`)**:
   - Middleware backend: condivide il dizionario tradotto in base alla lingua corrente (`translations`).
   - Hook frontend: [`useTranslations()`](file:///home/alan/Git/laravel-template/resources/js/hooks/useTranslations.tsx) restituisce la funzione `__(key, replacements)` per le stringhe localizzate:
     ```tsx
     const __ = useTranslations();
     const label = __('admin.title');
     ```

3. **Messaggi Flash di Sessione (`flash`)**:
   - Middleware backend: condivide `flash.success` e `flash.error`.
   - Consumati automaticamente dai sistemi di notifica (come i toast) o tramite l'hook personalizzato `useInertia`.

---

## 3. Frontend: Gestione dei Form

Il progetto adotta un'architettura a componenti e contesti per gestire i form in modo dichiarativo e pulito.

### A. Hook `useForm` di Inertia (`@inertiajs/react`)

Per istanziare lo stato reattivo del form si usa `useForm<T>` di `@inertiajs/react`:

```tsx
import { useForm } from "@inertiajs/react";
import type { TestataInputData } from "../../../types/generated";

const form = useForm<TestataInputData>({
    titolo: testata.titolo,
});

// Metodi e proprietà principali forniti da useForm:
// - form.data: dati correnti del form
// - form.setData(name, value): aggiornamento dati (supporta anche callback o oggetto)
// - form.errors: mappa degli errori di validazione restituiti dal backend
// - form.processing: boolean true durante l'invio HTTP
// - form.isDirty: boolean true se i valori differiscono da quelli iniziali
// - form.post(url, options) / form.patch(...) / form.put(...) / form.delete(...)
```

### B. Il Componente `<Form>` e `useFormContext`

Per evitare il *prop drilling* di `form`, `data`, `setData` ed `errors` verso ogni singolo campo di input, il progetto fornisce il wrapper [`resources/js/components/Form/useForm.tsx`](file:///home/alan/Git/laravel-template/resources/js/components/Form/useForm.tsx):

- Fornisce un `FormContext.Provider` con l'istanza del form.
- Previene il submit nativo del browser (`e.preventDefault()`).
- Esporta l'hook `useFormContext<T>()` per permettere ai campi di registrarsi autonomamente.

```tsx
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";

<Form form={form}>
    <Input name="titolo" label={__('admin.title')} />
    <Button 
        className="btn-primary" 
        disabled={!form.isDirty || form.processing}
        onClick={() => form.patch(localizedRoute('admin.testata.update', { id: testata.id }))}
    >
        {__('save')}
    </Button>
</Form>
```

### C. Integrazione con Componenti Input (`Input.tsx`)

Il componente [`resources/js/components/Input/Input.tsx`](file:///home/alan/Git/laravel-template/resources/js/components/Input/Input.tsx):
- Invoca internamente `useFormContext()` per ottenere `data`, `setData` ed `errors`.
- **Risolve proprietà annidate** (notazione dot-notation) tramite helper interno `getNestedValue`:
  - `name="roles.admin"` per checkbox o mappe;
  - `name="serie.0.numero"` per array e strutture complesse.
- **Gestione automatica degli errori di validazione**:
  - Aggiunge la classe Bootstrap `is-invalid` se `errors[name]` esiste.
  - Renderizza automaticamente il messaggio di errore con `<FieldError error={errors[name]} />`.
- Gestisce sia input di testo che checkbox (`type="checkbox"` usa `target.checked`).

### D. Il Custom Hook `useInertia` (`resources/js/hooks/useInertia.ts`)

Per chiamate e azioni standalone (es. pulsanti di attivazione, blocco, filtri o chiamate rapide senza un form completo), il progetto include l'hook [`useInertia`](file:///home/alan/Git/laravel-template/resources/js/hooks/useInertia.ts):

- **Pulizia parametri**: elimina in automatico parametri `null`, `undefined` o stringhe vuote (`cleanParams`).
- **Opzioni di default**: imposta `preserveState: true` e `preserveScroll: true` su tutte le richieste.
- **Integrazione Toast automatica**: intercetta i messaggi `flash.error` e li mostra all'utente tramite toast.
- **Helper tipizzato**: `getSuccessData()` e tipo `PageWithFlash<T>` per recuperare i payload di successo.

Esempio da [`User.tsx`](file:///home/alan/Git/laravel-template/resources/js/Pages/Admin/Users/User.tsx):

```tsx
import useInertia, { PageWithFlash } from "../../../hooks/useInertia.ts";
import { useToast } from "../../../components/Toast/useToast.tsx";

const { inertiaRouter } = useInertia();
const toast = useToast();

const onBlocca = () => {
    inertiaRouter.patch(
        localizedRoute('admin.user.blocca', { id: user.id }),
        {},
        {
            onSuccess: (page: PageWithFlash<string>) => {
                if (page.props.flash?.success) {
                    toast.success(page.props.flash.success);
                }
            },
        }
    );
};
```

---

## 4. Backend: Gestione delle Richieste e Risposte Inertia

Lato Laravel, i controller interagiscono con Inertia in due scenari: **Lettura (Render di pagine)** e **Scrittura (Mutazioni e Redirect)**.

### A. Render di Pagine (GET)

I metodi di lettura ritornano l'helper `inertia('NomeComponente', $props)` o `Inertia::render()`:

```php
use Inertia\Response;
use Inertia\ResponseFactory;

public function testata(int $id): Response|ResponseFactory
{
    return inertia('Admin/Fumetti/Testata', [
        'testata' => $this->queries->testata($id),
    ]);
}
```

- Il primo argomento è il path relativo all'interno di `resources/js/Pages/` (omettendo l'estensione `.tsx`).
- Il secondo argomento è l'array associativo delle props che saranno ricevute dal componente React tramite `usePage().props`.
- Le letture complesse vengono incapsulate in classi Query dedicate (es. `FumettiPageQueries`), mantenendo i controller puliti.

### B. Gestione delle Mutazioni (POST, PATCH, PUT, DELETE)

In Inertia, tutte le richieste di mutazione devono terminare con una risposta di **Redirect HTTP (302 o 303)**. Non si restituiscono mai risposte JSON dirette.

Esempio pratico da [`app/Areas/Admin/Fumetti/Presentation/Http/Controllers/FumettiController.php`](file:///home/alan/Git/laravel-template/app/Areas/Admin/Fumetti/Presentation/Http/Controllers/FumettiController.php):

```php
// 1. Validazione tipizzata tramite FormRequest dedicato
public function aggiornaTestata(int $id, TestataRequest $request): RedirectResponse
{
    // 2. Pattern CQRS: invio del comando con payload DTO
    dispatch_sync(new UpdateTestataCommand($id, $request->payloadData()));

    // 3. Rimanere sulla stessa pagina con messaggio flash di successo
    return back()->with('success', __('admin.comic_updated'));
}

public function eliminaTestata(int $id): RedirectResponse
{
    dispatch_sync(new DeleteTestataCommand($id));

    // 4. Redirect verso una route specifica localizzata
    return to_route(app()->getLocale() . '.admin.testate')
        ->with('success', __('admin.comic_deleted'));
}
```

### C. Validazione ed Errori

1. **FormRequest**:
   Crea sempre un FormRequest (es. `TestataRequest`). Aggiungi un metodo helper per ottenere il DTO Spatie tipizzato:
   ```php
   class TestataRequest extends FormRequest
   {
       public function rules(): array
       {
           return ['titolo' => ['required', 'string', 'max:255']];
       }

       public function payloadData(): TestataInputData
       {
           return TestataInputData::from($this->validated());
       }
   }
   ```
2. **Propagazione automatica**:
   Quando la validazione fallisce, Laravel lancia una `ValidationException`. Il middleware di Inertia intercetta l'eccezione e genera automaticamente un redirect indietro (`back()`) includendo gli errori nella sessione.
3. **Ricezione nel frontend**:
   Inertia popola istantaneamente l'oggetto `form.errors` nel frontend, consentendo a `<Input>` e `<FieldError>` di mostrare i messaggi all'utente senza alcuna logica aggiuntiva.

### D. Middleware `HandleInertiaRequests`

Il middleware [`HandleInertiaRequests`](file:///home/alan/Git/laravel-template/app/Infrastructure/Middleware/HandleInertiaRequests.php) estende `Inertia\Middleware` ed è registrato nella pipeline web.

```php
public function share(Request $request): array
{
    $user = Auth::user();
    $userData = $user ? UserData::fromModel($user) : null;
    $locale = app()->getLocale();

    return [
        ...parent::share($request),
        'locales' => config('app.locales'),
        'locale' => $locale,
        'defaultLocale' => config('app.fallback_locale'),
        'translations' => $this->getTranslations($locale),
        'auth' => [
            'user' => $userData,
            'capabilities' => app(AbilityResolver::class)->forUser($userData),
        ],
        'flash' => [
            // Lazy evaluation tramite closure per non valutare la sessione inutilmente
            'success' => fn () => $request->hasSession() ? $request->session()->get('success') : null,
            'error' => fn () => $request->hasSession() ? $request->session()->get('error') : null,
        ],
    ];
}
```

> [!NOTE]
> Per dati che non devono essere valutati a ogni richiesta parziale (come i messaggi flash), usa sempre funzioni anonime / closure (`fn () => ...`).

---

## 5. Buone Prassi e Pattern del Progetto

### 1. Routing Localizzato con Ziggy

Nel frontend non inserire **mai** URL con path hardcoded (es. `"/it/admin/testata"` o `"/admin/users"`).

Usa sempre:
- L'helper [`localizedRoute('nome.rotta', params)`](file:///home/alan/Git/laravel-template/resources/js/localizedRoute.ts).
- Oppure l'hook [`useRoutes()`](file:///home/alan/Git/laravel-template/resources/js/hooks/useRoutes.ts) per rotte fortemente tipizzate:
  ```tsx
  const routes = useRoutes();

  // Link tipizzato con parametri
  <Link href={routes.admin.testata(item.id)}>{__('edit')}</Link>

  // Chiamata con localizedRoute
  form.patch(localizedRoute('admin.testata.update', { id: testata.id }))
  ```

### 2. Conferme per Azioni Distruttive

Per le cancellazioni o operazioni critiche, utilizza l'hook `onBefore` del `router`:

```tsx
<button
    type="button"
    className="btn btn-outline-danger"
    onClick={() => router.delete(localizedRoute('admin.testata.delete', { id: testata.id }), {
        onBefore: () => window.confirm(__('admin.delete_confirm')),
    })}
>
    {__('delete')}
</button>
```

### 3. Filtri, Ricerca e Paginazione Live

Per pagine con filtri dinamici o cataloghi (come in [`Admin/Fumetti/Albi.tsx`](file:///home/alan/Git/laravel-template/resources/js/Pages/Admin/Fumetti/Albi.tsx) o [`App/Comics/Comics.tsx`](file:///home/alan/Git/laravel-template/resources/js/Pages/App/Comics/Comics.tsx)):

- Effettua una richiesta GET tramite `router.get`.
- Utilizza `preserveState: true` per mantenere lo stato dei controlli locali.
- Utilizza `preserveScroll: true` per non resettare lo scroll della finestra.
- Utilizza `replace: true` per aggiornare l'URL senza riempire la cronologia del browser ad ogni singolo carattere o selezione.

```tsx
const filtraAlbi = (testataId: number | null, serieId: number | null) => {
    router.get(routes.admin.albi(), {
        testata_id: testataId ?? "",
        serie_id: serieId ?? "",
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};
```

### 4. Gestione dello Stato Disabilitato dei Pulsanti

Nei form di modifica, disabilita il pulsante di salvataggio se il form non è stato modificato oppure se è già in corso un invio:

```tsx
<Button
    className="btn-primary"
    disabled={!form.isDirty || form.processing}
    onClick={onSubmit}
>
    {__('save')}
</Button>
```

### 5. Architettura CQRS sul Backend

Mantieni i controller leggeri delegando:
- **Letture**: a query classes (`*PageQueries`) che restituiscono DTO o array di DTO.
- **Scritture**: a command dedicati (`*Command`) eseguiti tramite `dispatch_sync(...)`.
- Il controller si limita a validare la richiesta HTTP, orchestrare la chiamata applicativa e restituire la risposta Inertia o il redirect.
