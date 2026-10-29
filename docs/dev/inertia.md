# Guida Tecnica a Inertia.js

Questo documento raccoglie le convenzioni pratiche per lavorare con **Inertia.js** nel template Laravel attuale. Il modello corrente è a lingua unica (`it`), usa route PHP normali e condivide dati tra backend e frontend tramite `Inertia\Middleware`.

---

## 1. Architettura generale e bootstrap

Il bootstrap del frontend è configurato in `resources/js/index.tsx`:

- risolve le pagine con `import.meta.glob('./Pages/**/*.tsx', { eager: true })`;
- applica layout automatici in base al percorso (`Admin/` vs. `App/`);
- usa le route Laravel con Ziggy, senza prefissi di locale.

Inertia collega Laravel e React in modo che i controller continuino a restituire pagine, mentre il frontend riceve props via `usePage()`.

---

## 2. Shared props e hook comuni

Il middleware `app/Infrastructure/Middleware/HandleInertiaRequests.php` condivide in modo automatico i dati globali:

- `locale`: locale attiva (`it` per default);
- `translations`: dizionario di `lang/it/*.php`;
- `auth.user` e `auth.capabilities`;
- `flash.success` e `flash.error`.

Questi dati sono usati in frontend tramite hook dedicati:

```tsx
import { usePage } from "@inertiajs/react";
import { useTranslations } from "@inertiajs/react";
import { useAuth } from "../hooks/useAuth";

const { props } = usePage();
const __ = useTranslations();
const user = useAuth();
```

`useTranslations()` restituisce la funzione per leggere le chiavi di traduzione; `useAuth()` espone l'utente autenticato e la sua capacità di accesso.

---

## 3. Gestione dei dati da pagina

Per leggere props specifiche, usa `usePage<T>()`:

```tsx
import { usePage } from "@inertiajs/react";

type Props = {
  title: string;
};

export default function Page() {
  const { title } = usePage<Props>().props;

  return <h1>{title}</h1>;
}
```

I componenti devono usare i tipi corretti e non duplicare strutture già presenti nel progetto. Quando il backend invia dati formattati, il frontend li riceve direttamente come props Inertia.

---

## 4. Form e richieste mutate

Il progetto usa `useForm` di `@inertiajs/react` per gestire form a stato reattivo:

```tsx
import { useForm } from "@inertiajs/react";

const form = useForm({
  name: user.name,
});

form.patch(route("admin.user.update", { id: user.id }));
```

Le route vanno costruite con `route(...)`, non con path hardcoded e non con prefissi di locale.

### Esempio di form completo

```tsx
import { useForm } from "@inertiajs/react";

const form = useForm({
  email: user.email,
});

const submit = () => {
  form.post(route("user.update", { id: user.id }));
};
```

In questo template i nomi route sono espliciti e stabili, per esempio `login`, `register`, `admin.users`, `admin.user.update`.

---

## 5. Route e navigazione

Le route sono definite nei file Laravel standard:

- `routes/web.php`
- `routes/web_auth.php`
- `routes/web_admin.php`

Esempi:

```php
Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::get('/admin.users', [UserController::class, 'index'])->name('admin.users');
```

Nel frontend:

```tsx
route("login")
route("admin.users")
route("admin.user.show", { id: user.id })
```

Non usare `config/routes.php`, `Route::prefix($locale)` o `localizedRoute(...)` in questo progetto.

---

## 6. Backend e risposta Inertia

I controller Laravel restituiscono pagine con `inertia(...)` o `Inertia::render()`:

```php
public function index(): Response|ResponseFactory
{
    return inertia('App/ExamplePage/ExamplePage');
}
```

Per mutazioni (`POST`, `PATCH`, `PUT`, `DELETE`), la convenzione del progetto è terminare con un redirect: `back()->with(...)` oppure `to_route('admin.users')->with(...)`.

```php
public function update(UserUpdateRequest $request): RedirectResponse
{
    // logica della mutazione

    return back()->with('success', __('user.updated'));
}
```

Le validazioni restano nei FormRequest; in caso di errore, Laravel e Inertia propagano gli errori nella sessione e nel form frontend.

---

## 7. Traduzioni e lingua

Il template è italiano; il locale attivo è impostato in `config/app.php` e la traduzione principale è in `lang/it/`.

Il middleware `HandleInertiaRequests` condivide `translations` con i componenti React, che usano `useTranslations()` per leggere il dizionario corrente.

```tsx
const __ = useTranslations();
return <span>{__('user.name')}</span>;
```

La regola è semplice: nessun testo visibile a utente va lasciato hardcoded nel componente senza passare da `lang/it`.

## 8. Buone pratiche

- usa `route()` e `to_route()` per tutte le navigazioni;
- mantieni i controller leggeri e sposta la logica non banale fuori da essi;
- usa `lang/it` per tutti i testi utente e i messaggi di validazione;
- imposta `preserveState: true` e `preserveScroll: true` nelle azioni di filtro e ricerca quando serve mantenere la UI coerente.

Questa documentazione è orientata al template attuale, dove le route sono stabili, il locale è singolo e il sistema di traduzione è centralizzato in `lang/it/`.
