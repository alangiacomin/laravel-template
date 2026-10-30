<?php

namespace App\Areas\Admin\Users\Presentation\Http\Requests;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', User::findOrFail($this->route('id'))) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'roles' => ['array'],
            'roles.*' => ['boolean'],
        ];
    }

    /**
     * Accetta solo ruoli noti.
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
