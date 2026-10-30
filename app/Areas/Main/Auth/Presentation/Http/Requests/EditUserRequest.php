<?php

namespace App\Areas\Main\Auth\Presentation\Http\Requests;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EditUserRequest
{
    public function __construct(
        public string $name,
    ) {}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
