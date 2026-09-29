# Workflow per agenti AI

## 1. Analisi e Checklist Pre-Piano (BLOCCANTE)

Prima di formulare qualsiasi piano operativo o toccare file di codice, l'agente deve completare la seguente checklist:

### A. Chiarimento Preventivo di Area e Contesto
Se non sono esplicitamente indicati nella richiesta dell'utente:
- È **tassativamente vietato** assumere un'area o un contesto di default.
- È **obbligatorio** chiedere chiarimento all'utente (utilizzando lo strumento `ask_question` o una domanda diretta bloccante) specificando:
  1. L'**Area di azione** (es. `Main`, `Admin`, ecc.).
  2. Il **Contesto / modulo di dominio** (es. `TodoList`, `Fumetti`, `Auth`, ecc.).

### B. Verifica delle Regole di Progetto
- Consultare [`docs/ai/architecture.md`](architecture.md) per identificare i namespace, i percorsi e le convenzioni esatte.
- Riconoscere che l'architettura per Aree/Contesti ha priorità assoluta su qualsiasi skill o convenzione standard di Laravel (es. non proporre mai model in `app/Models`).
- Verificare se esiste un playbook pertinente in [`docs/ai/playbooks/`](playbooks/README.md) (es. per nuove pagine o per nuovi model/migration).

---

## 2. Formulazione del Piano

Quando si propone un piano:
- Indicare chiaramente i file target con i loro percorsi completi (`app/Areas/{Area}/{Context}/...`).
- Esplicitare i nomi delle classi, dei namespace e delle tabelle del database (che devono essere in `snake_case` e al singolare).
- Se l'utente richiede conferma preventiva (*"Dammi un piano da confermare prima di modificare il codice"*), arrestarsi ed attendere la conferma esplicita prima di qualsiasi azione di scrittura.

---

## 3. Implementazione

- Applica la modifica più piccola e coerente che risolve il requisito, riusando helper, pattern e naming esistenti.
- Mantieni i tipi PHP rigorosi e aggiungi validazione nei punti di ingresso (Request).
- Non nascondere errori con catch generici, fallback silenziosi o ritorni che simulano il successo.
- Per modifiche cross-stack verifica tutte le superfici: route localizzate, controller, servizi, componenti React/Inertia, tipi TypeScript (`php artisan typescript:transform`), traduzioni `lang/` e test.

---

## 4. Verifica

Scegli ed esegui i controlli pertinenti in base ai file modificati:

- **PHP/backend**: `composer test` (PHPUnit) e `composer lint` (Laravel Pint e PHPStan).
- **Frontend**: `bun run build` o `npm run build`.
- **Routing**: controlla entrambe le lingue (`it`, `en`) e i nomi route.
- **Migration/configurazione**: esegui i test pertinenti verificando il funzionamento delle migration su SQLite/database di test.

Se un comando non può essere eseguito, indica il motivo e usa una verifica alternativa ragionevole. Non dichiarare verificato ciò che non è stato controllato.

---

## 5. Conclusione

Rileggi il diff (`git diff`), verifica che non contenga file spuri o modifiche involontarie e controlla lo stato del worktree. Riporta in modo conciso:
- cosa è cambiato (con link ai file modificati);
- quali verifiche sono state eseguite con successo;
- eventuali punti aperti o note operative.
