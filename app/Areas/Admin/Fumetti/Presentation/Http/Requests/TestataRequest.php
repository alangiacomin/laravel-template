<?php

namespace App\Areas\Admin\Fumetti\Presentation\Http\Requests;

use App\Areas\Admin\Fumetti\Application\Data\TestataInputData;
use Illuminate\Foundation\Http\FormRequest;

class TestataRequest extends FormRequest
{
    public function payloadData(): TestataInputData
    {
        return TestataInputData::from($this->validated());
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return ['titolo' => ['required', 'string', 'max:255']];
    }
}
