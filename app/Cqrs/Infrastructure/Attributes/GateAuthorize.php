<?php

namespace App\Cqrs\Infrastructure\Attributes;

use App\Shared\Infrastructure\Enums\GateEnum;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class GateAuthorize
{
    public function __construct(
        public GateEnum $ability,
        public array $arguments = []
    ) {}
}
