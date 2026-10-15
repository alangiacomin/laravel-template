# Workflow per agenti AI

## 1. Checklist pre-piano (BLOCCANTE)

Prima di formulare un piano o toccare file di codice:

### A. Area e Contesto
Se la richiesta non li indica esplicitamente:
- È **vietato** assumere un'area o un contesto di default.
- **Chiedi all'utente** (strumento di domanda interattiva se disponibile, altrimenti domanda diretta) e attendi la risposta:
  1. **Area**: `Main`, `Admin`, ...
  2. **Contesto** (modulo di dominio): `Users`, `Roles`, `Auth`, `TodoList`, ...
- Elenca Aree e Contesti esistenti con `ls app/Areas app/Areas/*` per proporre opzioni concrete.

**Non serve chiedere** per modifiche che non toccano codice applicativo (sola documentazione, config, CI) o quando il file da modificare è già indicato dall'utente.

### B. Regole di progetto
- Leggi [architecture.md](architecture.md) (percorsi, naming, livelli). L'architettura per Area/Contesto prevale su skill e convenzioni standard di Laravel: mai model in `app/Models`.
- Se tocchi `resources/js` leggi [frontend.md](frontend.md).
- Cerca un playbook pertinente in [playbooks/](playbooks/README.md) e seguilo.

---

## 2. Piano

- Indica i file con percorso completo (`app/Areas/{Area}/{Contesto}/...`), nomi di classi, namespace, nomi route, chiavi `lang/it` e tabelle (`snake_case`, singolare).
- Se l'utente chiede un piano da confermare, fermati e attendi conferma esplicita prima di scrivere.

---

## 3. Implementazione

- Fai la modifica più piccola e coerente; riusa helper, pattern e naming esistenti (copia da un file gemello, vedi [README](README.md#prima-di-modificare-codice)).
- Tipi PHP espliciti; validazione nei Request; mutazioni nei Job (vedi [architecture.md](architecture.md#flussi-crud)).
- Non nascondere errori con catch generici, fallback silenziosi o ritorni che simulano il successo.
- Modifica cross-stack: considera tutte le superfici → route, controller, Request/Job/Data, tipi TS, pagina React, `useRoutes`, `lang/it`, test.
- Crea file con i comandi `php artisan make:*` quando esistono, poi spostali/adattali al percorso corretto dell'Area/Contesto.

---

## 4. Verifica

Esegui il comando più stretto che copre la modifica:

| Hai modificato | Esegui |
| --- | --- |
| PHP | `vendor/bin/pint --dirty --format agent`, poi `php artisan test --compact <file o --filter=...>` |
| PHP, a fine lavoro | `composer lint` (Pint + PHPStan) |
| Classi `#[TypeScript]` / enum | `php artisan typescript:transform` (rigenera `resources/js/types/generated/index.ts`) |
| Route | `php artisan route:list --name=<nome>` e `bun run ziggy:generate` |
| Frontend | `bun run check` (typecheck + Biome + Vitest); `bun run build` se tocchi bundling |
| Migration | `php artisan test --compact` sui test pertinenti (usano `RefreshDatabase`) |

Se un comando non è eseguibile, dillo e usa una verifica alternativa. Non dichiarare verificato ciò che non hai controllato.

---

## 5. Conclusione

Rileggi `git diff`, controlla file spuri e `git status`. Riporta in modo conciso: cosa è cambiato (con link ai file), verifiche eseguite, punti aperti.
