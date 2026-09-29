<?php

namespace App\Areas\Admin\Fumetti\Application\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AlboInputData extends Data
{
    /**
     * @param list<AlboPubblicazioneInputData> $pubblicazioni
     */
    public function __construct(
        public string $titolo,
        public int|string $testata_id,
        #[DataCollectionOf(AlboPubblicazioneInputData::class)]
        public array $pubblicazioni = [],
    ) {}
}
