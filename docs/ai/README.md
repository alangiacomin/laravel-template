# Documentazione per agenti AI

Vendor-neutral: valida per Claude Code, GitHub Copilot, Junie, Gemini e altri. Le regole vincolanti sono in `AGENTS.md`; qui c'è il dettaglio operativo.

## Gerarchia delle fonti

1. Richiesta e vincoli espliciti dell'utente.
2. `AGENTS.md` e questa cartella (`docs/ai/`).
3. Convenzioni e skill generiche di Laravel: solo sussidiarie. **In caso di conflitto vince `docs/ai/`** (es. i model NON vanno in `app/Models`).
4. Convenzioni osservabili nel codice esistente.

Se due fonti sono in conflitto, segnala il conflitto e segui quella con priorità maggiore.

## Cosa leggere e quando

| Documento | Leggilo quando |
| --- | --- |
| [workflow.md](workflow.md) | **Sempre**, prima di proporre un piano o toccare file (chiedi Area e Contesto) |
| [architecture.md](architecture.md) | Tocchi PHP: percorsi, naming, livelli, routing, model, tipi TS, traduzioni |
| [frontend.md](frontend.md) | Tocchi `resources/js`: pagine, hook, form, test frontend |
| [playbooks/](playbooks/README.md) | Il compito coincide con un playbook (nuova pagina, nuovo model, nuova scrittura CRUD) |
| [adapters.md](adapters.md) | Devi collegare un nuovo strumento/IDE a queste regole |

## Prima di modificare codice

1. Esegui la checklist in [workflow.md](workflow.md).
2. Cerca nel repo un'implementazione simile e copiane struttura e naming. Riferimenti pronti: contesto backend `app/Areas/Admin/Users`, pagina semplice `resources/js/Pages/App/ExamplePage`, pagina con form `resources/js/Pages/Admin/Users/User.tsx`, test `tests/Feature/Admin/UserControllerTest.php`.
3. Controlla `git status` e preserva le modifiche non pertinenti.
4. Decidi in anticipo quale comando dimostra che la modifica funziona (tabella in [workflow.md](workflow.md#4-verifica)).

## Manutenzione

Se il codice smentisce una regola, verifica il codice, poi correggi la doc o l'implementazione: non lasciare ambiguità. Documenta decisioni, confini e comandi, non ogni dettaglio del codice.
