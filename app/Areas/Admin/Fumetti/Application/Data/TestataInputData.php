<?php

namespace App\Areas\Admin\Fumetti\Application\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TestataInputData extends Data
{
    public function __construct(
        public string $titolo,
    ) {}
}
