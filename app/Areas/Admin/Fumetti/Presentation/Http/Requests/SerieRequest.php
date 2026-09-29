<?php

namespace App\Areas\Admin\Fumetti\Presentation\Http\Requests;

use App\Areas\Admin\Fumetti\Application\Data\SerieInputData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SerieRequest extends FormRequest
{
    public function payloadData(): SerieInputData
    {
        return SerieInputData::from($this->validated());
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'testata_id' => ['required', 'integer', Rule::exists('testate', 'id')->whereNull('deleted_at')],
            'titolo' => ['required', 'string', 'max:255'],
        ];
    }
}
