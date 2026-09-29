<?php

namespace App\Areas\Main\Fumetti\Application\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CatalogComicPublicationData extends Data
{
    public function __construct(
        public CatalogSerieOptionData $serie,
        public int $numero,
        public ?int $numeroGruppo,
        public ?string $dataPubblicazione,
    ) {}
}
