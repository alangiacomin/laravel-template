# Aggiungere una nuova pagina

## Precondizioni

Identifica l'area funzionale, il controller o caso d'uso previsto, il nome
logico della route e i componenti Inertia necessari.

## Procedura

1. Scegli il file di routing corretto: `routes/web.php`, `routes/web_auth.php`
   oppure `routes/web_admin.php`.
2. Registra la route con un path stabile e un nome logico chiaro, senza prefisso
   di locale.
3. Crea o aggiorna controller e classi nell'area corretta, seguendo la
   struttura dei livelli già usata.
4. Crea la pagina frontend e i tipi necessari, riusando componenti esistenti.
5. Aggiungi le chiavi di traduzione in `lang/it` per ogni testo visibile.
6. Aggiungi test per autorizzazione, risposta e comportamento non banale.

## Verifica

Controlla la route registrata, il nome logico (`route('nome')`), la build
frontend e i test backend pertinenti. Aggiorna `docs/dev/development.md` se il
flusso introduce una nuova convenzione.
