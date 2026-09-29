<?php

namespace App\Areas\Admin\Fumetti\Application\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AlboPubblicazioneInputData extends Data
{
    public function __construct(
        public int $serie_id,
        public int $numero,
        public ?int $numero_gruppo = null,
        public ?string $data_pubblicazione = null,
    ) {}
}
