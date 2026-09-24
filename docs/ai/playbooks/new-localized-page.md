# Aggiungere una pagina localizzata

## Precondizioni

Identifica l'area funzionale, il controller o caso d'uso previsto, il nome
logico della route e i componenti Inertia necessari.

## Procedura

1. Aggiungi il path con la stessa chiave logica in `config/routes.php` per `it`
   ed `en`.
2. Crea o aggiorna controller e classi nell'area corretta, seguendo la
   struttura dei livelli già usata.
3. Registra la route nel file `routes/` appropriato dentro il gruppo locale.
4. Crea la pagina frontend e i tipi necessari, riusando componenti esistenti.
5. Aggiungi le chiavi di traduzione in `lang/it` e `lang/en` per ogni testo
   visibile.
6. Aggiungi test per autorizzazione, risposta e comportamento non banale.

## Verifica

Controlla entrambe le URL localizzate, i nomi `it.<route>` ed `en.<route>`,
la build frontend e i test backend pertinenti. Aggiorna
`docs/development.md` se il flusso introduce una nuova convenzione.
