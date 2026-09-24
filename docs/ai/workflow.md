# Workflow per agenti AI

## Analisi

Riformula mentalmente il risultato richiesto e delimita il cambiamento. Leggi
prima i file direttamente coinvolti, poi cerca usi, test e implementazioni
analoghe. Se il requisito è ambiguo, scegli l'interpretazione più conservativa
e rendi esplicita l'assunzione.

## Implementazione

Applica la modifica più piccola che risolve il requisito, riusando helper,
pattern e naming esistenti. Mantieni i tipi e aggiungi validazione nei punti
di ingresso. Non nascondere errori con catch generici, fallback silenziosi o
ritorni che simulano il successo.

Per modifiche cross-stack verifica tutte le superfici: route, controller,
servizi, componenti, tipi, traduzioni, test e documentazione.

## Verifica

Scegli i controlli in base ai file modificati:

- PHP/backend: `composer test` e, quando pertinente, `composer lint`;
- frontend: `bun run build` oppure `npm run build`;
- routing: controlla entrambe le lingue e i nomi route;
- migration/configurazione: esegui i test pertinenti senza usare segreti reali.

Se un comando non può essere eseguito, indica il motivo e usa una verifica
alternativa ragionevole. Non dichiarare verificato ciò che non è stato
controllato.

## Conclusione

Rileggi il diff, verifica che non contenga file generati o modifiche
involontarie e controlla lo stato del worktree. Riporta in modo conciso:
cosa è cambiato, quali verifiche sono passate e quali sono rimaste bloccate.
