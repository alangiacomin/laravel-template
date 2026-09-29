<?php

namespace App\Areas\Main\Fumetti\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComicCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:200'],
            'testata_id' => ['nullable', 'integer', Rule::exists('testate', 'id')->whereNull('deleted_at')],
            'serie_id' => ['nullable', 'integer', Rule::exists('serie', 'id')->whereNull('deleted_at')],
            'year' => ['nullable', 'integer', 'between:1900,2100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
