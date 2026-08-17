<?php

namespace App\Areas\Main\Auth\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get a custom body parameters description for Scribe.
     *
     * @return array<string, array<string, string>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Il nome dell\'utente.',
                'example' => 'Mario Rossi',
            ],
        ];
    }
}
