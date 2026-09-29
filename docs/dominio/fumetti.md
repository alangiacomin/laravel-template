# Dominio dei fumetti

## Modello

Il catalogo è composto da testate, serie, albi e associazioni albo-serie.
Ogni elemento, incluse le associazioni, ha `created_at`, `updated_at` e
`deleted_at`; la cancellazione è logica.

| Elemento | Dati di dominio | Relazioni |
| --- | --- | --- |
| **Testata** | Titolo | Può avere zero o più serie. |
| **Serie** | Titolo | Appartiene a una sola testata; può contenere zero o più albi. |
| **Albo** | Titolo | Può comparire in zero o più serie. Lo stesso titolo può ricorrere in serie diverse. |
| **AlboSerie** | Numero nella serie, numero di gruppo facoltativo, data di pubblicazione facoltativa | Collega un albo a una serie; i dati editoriali appartengono alla specifica associazione. |

Una testata può essere creata senza serie. Un albo può essere creato senza
associazioni; quando è presente in più serie non viene duplicato, ma ogni
associazione conserva i propri dati di pubblicazione.

Nel portale admin la creazione di un albo richiede di selezionare prima una
testata e poi una sua serie; viene creata anche l'associazione con il relativo
numero. La pagina di modifica consente di aggiungere altre serie e completare
i dettagli delle associazioni.

Il catalogo pubblico presenta gli albi in un'unica pagina, con ricerca per
titolo, testata o serie e filtri combinabili per testata, serie e anno di
pubblicazione. Ogni albo compare una sola volta anche quando ha più
associazioni; date e pubblicazioni assenti sono indicate senza inventare dati.

## Regole

- `serie.testata_id` è obbligatorio.
- `albo_serie.albo_id`, `serie_id` e `numero` sono obbligatori.
- `numero_gruppo` è un intero facoltativo, per esempio l'anno di una
  numerazione; non è un tipo di numerazione né un'etichetta di sequenza.
- `data_pubblicazione` può essere assente.
- La rimozione di testate, serie, albi o associazioni imposta `deleted_at`;
  non elimina fisicamente i record. Eliminando una testata, il command elimina
  logicamente le sue serie e le associazioni di quelle serie. Eliminando una
  serie o un albo, vengono eliminate logicamente le associazioni collegate.
- Un'associazione rimossa durante la modifica di un albo viene conservata come
  eliminata logicamente. Se la stessa coppia albo-serie viene riaggiunta, viene
  riattivata la relativa associazione.
- La persistenza non impone l'unicità dei titoli né del numero tra albi o
  serie: questi vincoli non sono definiti dai requisiti.

## CQRS

I controller gestiscono i flussi HTTP: validano i dati con FormRequest,
invocano i command per ogni scrittura e compongono la risposta. I command
contengono tutte le mutazioni, incluse creazione, aggiornamento e cancellazione
logica. Le query applicative sono dedicate alla lettura e non modificano dati.

## Schema

Lo schema visuale è mantenuto in [`fumetti.mmd`](./fumetti.mmd).
