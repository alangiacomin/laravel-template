# Frontend (Inertia + React + TypeScript)

Prima di scrivere codice attiva la skill `inertia-react-development` per le API Inertia v3; in caso di conflitto con questo file vincono le regole di progetto qui sotto.

## Struttura

```
resources/js/
├── index.tsx / App.tsx      # bootstrap Inertia; risolve Pages/**/*.tsx; layout in base alla cartella
├── Pages/
│   ├── App/<Pagina>/<Pagina>.tsx     # area pubblica (layout App)
│   ├── Admin/<Area>/<Pagina>.tsx     # area admin (layout Admin)
│   ├── App|Admin/components/         # Layout, Page, Navbar, Breadcrumb
│   └── page.types.ts                 # SharedPageProps
├── components/              # componenti riusabili (Button, Input, Form, Toast, ...)
├── hooks/                   # useTranslations, useAuth, useRoutes, useInertia, useEcho, useErrors
├── types/generated/index.ts # GENERATO da `php artisan typescript:transform`: non modificare a mano
└── ziggy.js                 # GENERATO da `bun run ziggy:generate`
```

Il nome passato dal controller (`inertia('App/ExamplePage/ExamplePage')`) è il percorso sotto `Pages/` senza estensione. Cerca prima un componente riusabile in `components/` e nelle cartelle `components` dell'area.

## Pagina minima

Controller (`app/Areas/Main/ExamplePage/Presentation/Http/Controllers/ExamplePageController.php`):

```php
public function index(): Response|ResponseFactory
{
    return inertia('App/ExamplePage/ExamplePage');
}
```

Pagina (`resources/js/Pages/App/ExamplePage/ExamplePage.tsx`):

```tsx
import type { FC, ReactNode } from "react";
import useTranslations from "../../../hooks/useTranslations.tsx";
import Page from "../components/Page/Page.tsx";

const ExamplePage: FC = (): ReactNode => {
  const __ = useTranslations();

  return (
    <Page browserTitle={__("example_page.browser_title")}>
      <h1>{__("example_page.title")}</h1>
    </Page>
  );
};

export default ExamplePage;
```

Per le pagine Admin importa `Page` da `Pages/Admin/components/Page/Page.tsx`.

## Regole

- Import con estensione (`.tsx`/`.ts`) e percorsi relativi, come nei file esistenti; `import type` per i soli tipi.
- Hook di progetto (default export salvo `useRoutes`): `useTranslations` (`hooks/useTranslations.tsx`), `useAuth` (`hooks/useAuth.tsx`, espone `user.can(GateEnum.X)`), `{ useRoutes }` (`hooks/useRoutes.ts`), `useInertia`. **`useTranslations` NON viene da `@inertiajs/react`.**
- Testi: sempre `__("file.chiave")` con chiave in `lang/it/file.php`. Mai stringhe hardcoded.
- URL: `route("nome.route", { id })` (Ziggy) o `useRoutes()`; ogni nuova route navigabile va aggiunta in `useRoutes.ts`. Mai path a mano, mai prefissi di locale.
- Props di pagina tipizzate con i tipi generati: `import type { AdminUserData } from "../../../types/generated"` e `usePage<{ user: AdminUserData }>().props`. Non ridefinire a mano strutture esistenti.
- Permessi: `useAuth()?.can(GateEnum.USER_EDIT)` con gli enum generati.
- Deferred props (`Inertia::defer`) → mostra uno skeleton/pulsing mentre carica.
- Dopo una mutazione il backend risponde con redirect + flash; il frontend lo legge in `onSuccess` (`page.props.flash?.success`) e lo mostra con `useToast().success(...)` (esempio: `onBlocca` in `Pages/Admin/Users/User.tsx`; tipo `PageWithFlash` in `hooks/useInertia.ts`).

## Shared props (`app/Infrastructure/Middleware/HandleInertiaRequests.php`)

`locale`, `translations` (da `lang/it/*.php`), `auth.user`, `auth.capabilities`, `flash.success`, `flash.error`. Tipizzate in `Pages/page.types.ts` (`SharedPageProps`). Per aggiungerne una: `share()` + tipo in `page.types.ts`.

## Form

Esempio reale: `Pages/Admin/Users/User.tsx`: `useForm` di `@inertiajs/react`, passato a `<Form form={form}>` (`components/Form/useForm.tsx`) con campi `components/Input` (prop `name`).

```tsx
const form = useForm({ name: user.name });
form.patch(route("admin.user.update", { id: user.id }));
```

Gli errori di validazione dei FormRequest arrivano in `form.errors`. Per filtri/ricerca usa `preserveState: true` e `preserveScroll: true`.

## Test frontend

Vitest + Testing Library, file `*.test.ts(x)` accanto al codice (esempio: `hooks/useTranslations.test.tsx`, che mocka `usePage` con `vi.mock("@inertiajs/react", ...)`). Setup in `resources/js/test`.

## Verifica

`bun run check` (typecheck + Biome + Vitest). `bun run lint:fix` applica le correzioni sicure. `bun run build` per verificare il bundle.
