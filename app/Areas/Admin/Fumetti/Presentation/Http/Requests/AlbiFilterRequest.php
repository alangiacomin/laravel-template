<?php

namespace App\Areas\Admin\Fumetti\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlbiFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'testata_id' => ['nullable', 'integer', Rule::exists('testate', 'id')->whereNull('deleted_at')],
            'serie_id' => ['nullable', 'integer', Rule::exists('serie', 'id')->whereNull('deleted_at')],
        ];
    }
}
