# Istruzioni per agenti AI

Queste istruzioni valgono per qualsiasi agente o modello che lavori nel
repository. La fonte completa è [`docs/ai/README.md`](docs/ai/README.md);
eventuali configurazioni specifiche di Copilot, Junie o altri strumenti devono
solo richiamarla e non duplicarne le regole.

## Regole essenziali

- Leggi `README.md`, la documentazione pertinente in `docs/` e
  `docs/ai/architecture.md` prima di modificare codice.
- Esamina sempre implementazioni simili e il worktree corrente prima di
  intervenire. Non sovrascrivere modifiche preesistenti di altri autori.
- Mantieni la separazione per aree e livelli `Presentation`, `Application`,
  `Domain` e `Infrastructure` quando applicabile.
- Rispetta il routing localizzato: aggiorna `config/routes.php` per ogni
  locale e registra le route nel file `routes/` corretto.
- Usa le traduzioni in `lang/` per ogni testo destinato all'utente; mantieni
  coerenti le chiavi `it` ed `en`.
- Preferisci modifiche piccole, tipizzate e coerenti con il codice esistente.
  Non introdurre dipendenze o astrazioni senza necessità.
- Dopo una modifica esegui i controlli pertinenti descritti in
  `docs/ai/workflow.md` e riporta chiaramente eventuali controlli non eseguiti
  o falliti.
- Aggiorna la documentazione quando cambiano comportamento, convenzioni,
  comandi o struttura del progetto.
- Non modificare segreti, `.env` locale, file generati o dipendenze installate
  salvo richiesta esplicita.

## Playbook

Per attività ricorrenti usa i playbook in [`docs/ai/playbooks`](docs/ai/playbooks).
Sono procedure concettuali, non dipendono da un particolare set di tool.
