# Documentazione per agenti AI

Questa documentazione descrive come un agente AI deve orientarsi e operare nel
template Laravel. È **vendor-neutral**: le regole valgono per GitHub Copilot,
Junie, Claude Code, Gemini e altri agenti, indipendentemente dal modello o dagli
strumenti disponibili.

## Gerarchia delle fonti

1. Requisiti dell'utente e vincoli espliciti della richiesta.
2. `AGENTS.md` e le istruzioni dell'ambiente agente.
3. Questa documentazione e la documentazione tecnica in `docs/`.
4. Convenzioni osservabili nel codice esistente.

In caso di conflitto va segnalato il conflitto e va seguita la fonte con
priorità maggiore. Le istruzioni specifiche di un prodotto sono adapter:
possono indicare dove trovare questa documentazione, ma non devono cambiarne il
significato.

## Prima di modificare codice

1. Individua i file e i test coinvolti.
2. Leggi la documentazione relativa all'area.
3. Cerca un'implementazione simile già presente.
4. Controlla lo stato del worktree e preserva le modifiche non pertinenti.
5. Definisci il controllo minimo che dimostrerà che la modifica funziona.

## Documenti

- [Architettura e invarianti](architecture.md)
- [Workflow per agenti](workflow.md)
- [Playbook disponibili](playbooks/README.md)
- [Compatibilità tra agenti](adapters.md)

## Principio di manutenzione

La documentazione per l'AI deve spiegare decisioni, confini e verifiche, non
replicare ogni dettaglio del codice. Quando una regola viene smentita dal
repository, prima si verifica il codice e poi si aggiorna la documentazione
oppure si corregge l'implementazione: non si lascia ambiguità.
