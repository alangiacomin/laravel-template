<?php

namespace App\Areas\Admin\Fumetti\Application\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AlboPubblicazioneData extends Data
{
    public function __construct(
        public int $id,
        public int $numero,
        public ?int $numero_gruppo,
        public ?string $data_pubblicazione,
    ) {}
}
