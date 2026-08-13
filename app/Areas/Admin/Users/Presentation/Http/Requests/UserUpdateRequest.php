<?php

namespace App\Areas\Admin\Users\Presentation\Http\Requests;

use App\Shared\Infrastructure\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can(PermissionEnum::USER_UPDATE);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'roles' => ['array'],
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
