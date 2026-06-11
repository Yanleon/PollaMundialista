<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatchResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'home_score' => ['required', 'integer', 'min:0', 'max:30'],
            'away_score' => ['required', 'integer', 'min:0', 'max:30'],
            'status' => ['required', Rule::in(['finished'])],
        ];
    }
}
