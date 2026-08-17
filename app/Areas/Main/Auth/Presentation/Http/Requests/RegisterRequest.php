<?php

namespace App\Areas\Main\Auth\Presentation\Http\Requests;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegisterRequest extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $password_confirmation,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
