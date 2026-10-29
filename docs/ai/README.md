# Documentazione per agenti AI

Questa documentazione descrive come un agente AI deve orientarsi e operare nel
template Laravel. È **vendor-neutral**: le regole valgono per GitHub Copilot,
Junie, Claude Code, Gemini e altri agenti, indipendentemente dal modello o dagli
strumenti disponibili.

## Gerarchia delle fonti

1. **Requisiti dell'utente** e vincoli espliciti della richiesta.
2. **Questa documentazione (`docs/ai/`)** e le istruzioni sovrane in `AGENTS.md`.
3. **Convenzioni e skill generiche del framework**: hanno valore sussidiario. **In caso di conflitto, le regole in `docs/ai/` prevalgono SEMPRE sulle convenzioni generiche di Laravel e sulle skill** (es. l'architettura modulare per Area e Contesto ha priorità sul posizionamento piatto standard `app/Models`).
4. **Convenzioni osservabili nel codice esistente**.

In caso di conflitto va segnalato il conflitto e va seguita la fonte con priorità maggiore. Le istruzioni specifiche di un prodotto sono adapter: possono indicare dove trovare questa documentazione, ma non devono cambiarne il significato.

## Prima di proporre un piano o modificare codice

1. **Esegui la checklist pre-piano in [workflow.md](workflow.md)**: chiedi SEMPRE chiarimento su **Area** e **Contesto** all'utente se non esplicitamente indicati prima di iniziare o proporre un piano.
2. Individua i file e i test coinvolti consultando [architecture.md](architecture.md).
3. Leggi la documentazione relativa all'area e al contesto di dominio.
4. Cerca un'implementazione simile già presente nel repository.
5. Controlla lo stato del worktree e preserva le modifiche non pertinenti.
6. Definisci il controllo minimo che dimostrerà che la modifica funziona.

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
