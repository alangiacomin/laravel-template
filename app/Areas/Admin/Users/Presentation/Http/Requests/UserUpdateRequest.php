<?php

namespace App\Areas\Admin\Users\Presentation\Http\Requests;

use App\Models\User;
use App\Shared\Infrastructure\Enums\PermissionEnum;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

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
            'roles.*' => ['boolean'],
        ];
    }

    /**
     * Accetta solo ruoli noti. Chi non è super-admin non può cambiare i ruoli
     * privilegiati (super-admin, admin) né i propri ruoli.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $roles = $this->input('roles', []);

                if (!is_array($roles)) {
                    return;
                }

                $knownRoles = array_map(fn (RoleEnum $role) => $role->value, RoleEnum::cases());

                if (array_diff(array_keys($roles), $knownRoles) !== []) {
                    $validator->errors()->add('roles', __('validation.in', ['attribute' => 'roles']));

                    return;
                }

                if (Auth::user()->hasRole(RoleEnum::SUPER_ADMIN->value)) {
                    return;
                }

                $target = User::find($this->route('id'));
                $currentRoles = $target?->getRoleNames()->all() ?? [];
                $requestedRoles = array_keys(array_filter(
                    $roles,
                    fn ($granted) => filter_var($granted, FILTER_VALIDATE_BOOLEAN),
                ));

                $privilegedRoles = [RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value];
                $changed = array_merge(
                    array_diff($currentRoles, $requestedRoles),
                    array_diff($requestedRoles, $currentRoles),
                );

                $changesPrivilegedRole = array_intersect($changed, $privilegedRoles) !== [];
                $changesOwnRoles = $changed !== [] && $target?->is(Auth::user());

                if ($changesPrivilegedRole || $changesOwnRoles) {
                    $validator->errors()->add('roles', __('validation.in', ['attribute' => 'roles']));
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
            'name' => [
                'description' => 'Il nome dell\'utente.',
                'example' => 'Mario Rossi',
            ],
        ];
    }
}
