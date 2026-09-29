<?php

namespace App\Areas\Admin\Fumetti\Application\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TestataDetailData extends Data
{
    /**
     * @param list<AlboOptionData> $albi
     */
    public function __construct(
        public int $id,
        public string $titolo,
        #[DataCollectionOf(AlboOptionData::class)]
        public array $albi,
    ) {}
}
