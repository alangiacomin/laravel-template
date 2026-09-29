<?php

namespace App\Areas\Admin\Fumetti\Application\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TestataSummaryData extends Data
{
    /**
     * @param list<AlboOptionData> $albi
     */
    public function __construct(
        public int $id,
        public string $titolo,
        public int $albi_count,
        #[DataCollectionOf(AlboOptionData::class)]
        public array $albi,
    ) {}
}
