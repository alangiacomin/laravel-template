# Sviluppo

## Dove metto le cose

| Cosa | Dove |
| --- | --- |
| Route | `routes/web.php` (pubblico), `routes/web_auth.php` (auth), `routes/web_admin.php` (admin) |
| Controller, Request, Job, Data | `app/Areas/{Area}/{Contesto}/{Presentation\|Application}/...` |
| Model + migration | `app/Areas/{Area}/{Contesto}/Infrastructure/Persistence/Eloquent/Models/` + `database/migrations/` (tabella al singolare) |
| Pagina React | `resources/js/Pages/App/` (pubblico) o `resources/js/Pages/Admin/` |
| Testi utente | `lang/it/*.php` (lingua unica, nessun prefisso nelle URL) |
| Tipi TypeScript | `php artisan typescript:transform` |
| Test | `tests/Feature`, `tests/Unit`; frontend `*.test.tsx` accanto al codice |

Nel frontend le URL si costruiscono con `route('nome.route')` (Ziggy), mai con path scritti a mano.

## Comandi

```bash
composer run dev          # tutto l'ambiente
bun run dev               # solo Vite
composer test             # PHPUnit
composer lint             # Pint + PHPStan
bun run check             # typecheck + Biome + Vitest
bun run lint:fix          # correzioni Biome
```

PhpStorm: installa il plugin **Biome** e attivalo in Settings → Languages & Frameworks → Biome (modalità automatica).

## Prima di una PR

- `composer test` e `composer lint` passano
- `bun run check` passa
- nuovi testi in `lang/it/`
