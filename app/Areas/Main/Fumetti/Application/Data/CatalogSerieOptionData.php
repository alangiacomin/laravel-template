<?php

namespace App\Areas\Main\Fumetti\Application\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CatalogSerieOptionData extends Data
{
    public function __construct(
        public int $id,
        public string $titolo,
    ) {}
}
