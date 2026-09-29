<?php

namespace App\Areas\Admin\Fumetti\Application\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AlboSummaryData extends Data
{
    /**
     * @param list<AlboSummaryPubblicazioneData> $pubblicazioni
     */
    public function __construct(
        public int $id,
        public string $titolo,
        public TestataOptionData $testata,
        #[DataCollectionOf(AlboSummaryPubblicazioneData::class)]
        public array $pubblicazioni,
    ) {}
}
