<?php

namespace App\Areas\Admin\Fumetti\Presentation\Http\Requests;

use App\Areas\Admin\Fumetti\Application\Data\AlboInputData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlboRequest extends FormRequest
{
    public function payloadData(): AlboInputData
    {
        return AlboInputData::from($this->validated());
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'titolo' => ['required', 'string', 'max:255'],
            'serie' => $this->isMethod('post')
                ? ['required', 'array', 'min:1']
                : ['array'],
            'serie.*.serie_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('serie', 'id')->whereNull('deleted_at'),
            ],
            'serie.*.numero' => ['required', 'integer', 'min:1'],
            'serie.*.numero_gruppo' => ['nullable', 'integer', 'min:1'],
            'serie.*.data_pubblicazione' => ['nullable', 'date'],
        ];
    }
}
