<?php

namespace App\Areas\Main\Fumetti\Application\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CatalogComicData extends Data
{
    /**
     * @param  list<CatalogComicPublicationData>  $pubblicazioni
     */
    public function __construct(
        public int $id,
        public string $titolo,
        public CatalogOptionData $testata,
        #[DataCollectionOf(CatalogComicPublicationData::class)]
        public array $pubblicazioni,
    ) {}
}
