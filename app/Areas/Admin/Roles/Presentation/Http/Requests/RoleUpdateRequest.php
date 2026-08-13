<?php

namespace App\Areas\Admin\Roles\Presentation\Http\Requests;

use App\Shared\Infrastructure\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can(PermissionEnum::ROLE_UPDATE);
    }

    public function rules(): array
    {
        return [
            'permissions' => ['array'],
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
            'permissions' => [
                'description' => 'Le autorizzazioni da assegnare al ruolo.',
                'example' => '[\'admin\', \'user\']',
            ],
        ];
    }
}
