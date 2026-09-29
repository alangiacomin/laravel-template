<?php

namespace App\Areas\Admin\Fumetti\Application\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AlboDetailData extends Data
{
    /**
     * @param list<AlboDetailPubblicazioneData> $pubblicazioni
     */
    public function __construct(
        public int $id,
        public string $titolo,
        public TestataNameData $testata,
        #[DataCollectionOf(AlboDetailPubblicazioneData::class)]
        public array $pubblicazioni,
    ) {}
}
