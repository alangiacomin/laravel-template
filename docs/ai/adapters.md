# Compatibilità tra agenti

Il contenuto normativo è in `AGENTS.md` e `docs/ai/`. Un'integrazione specifica
per un prodotto deve essere un file breve che:

- indica la lettura di `AGENTS.md`;
- collega `docs/ai/README.md`;
- non copia regole o comandi in modo indipendente;
- non richiede un modello, un'estensione o un tool proprietario.

Esempi di adapter possibili:

- GitHub Copilot: `.github/copilot-instructions.md`;
- agenti che riconoscono `AGENTS.md`: usano direttamente il file root;
- Junie o altri IDE: configurazione del progetto che punta a `AGENTS.md` e
  `docs/ai/`.

Non è possibile garantire che ogni agente legga automaticamente gli stessi
nomi di file. Per questo il repository mantiene una fonte esplicita e
vendor-neutral, mentre ogni team può registrarla nell'entrypoint previsto dal
proprio strumento.
