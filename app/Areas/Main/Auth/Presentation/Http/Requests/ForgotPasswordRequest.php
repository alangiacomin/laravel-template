<?php

namespace App\Areas\Main\Auth\Presentation\Http\Requests;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ForgotPasswordRequest extends Data
{
    public function __construct(
        public string $email,
    ) {}

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public static function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }
}
