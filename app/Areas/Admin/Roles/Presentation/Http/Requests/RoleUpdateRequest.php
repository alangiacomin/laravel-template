<?php

namespace App\Areas\Admin\Roles\Presentation\Http\Requests;

use App\Shared\Infrastructure\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

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
            'permissions.*' => ['boolean'],
        ];
    }

    /**
     * Accetta solo permessi noti.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $permissions = $this->input('permissions', []);

                if (!is_array($permissions)) {
                    return;
                }

                $knownPermissions = array_map(fn (PermissionEnum $permission) => $permission->value, PermissionEnum::cases());

                if (array_diff(array_keys($permissions), $knownPermissions) !== []) {
                    $validator->errors()->add('permissions', __('validation.in', ['attribute' => 'permissions']));
                }
            },
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
                'description' => __('validation.attributes.permissions'),
                'example' => '[\'admin\', \'user\']',
            ],
        ];
    }
}
