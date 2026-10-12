<?php

namespace App\Areas\Admin\Roles\Presentation\Http\Requests;

use App\Shared\Infrastructure\Enums\PermissionEnum;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Spatie\Permission\Models\Role;

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
     * Accetta solo permessi noti. Chi non è super-admin non può modificare
     * il ruolo super-admin né i ruoli che possiede.
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

                    return;
                }

                $user = Auth::user();

                if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
                    return;
                }

                $role = Role::find($this->route('id'));

                if ($role !== null && ($role->name === RoleEnum::SUPER_ADMIN->value || $user->hasRole($role->name))) {
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
