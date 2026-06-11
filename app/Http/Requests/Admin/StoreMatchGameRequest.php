<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMatchGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase' => ['required', 'string', 'max:50'],
            'group_name' => ['nullable', 'string', 'max:20'],
            'home_team_id' => ['required', 'integer', 'exists:teams,id', 'different:away_team_id'],
            'away_team_id' => ['required', 'integer', 'exists:teams,id', 'different:home_team_id'],
            'match_date' => ['required', 'date'],
            'prediction_deadline' => ['required', 'date', 'before_or_equal:match_date'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:30'],
            'away_score' => ['nullable', 'integer', 'min:0', 'max:30'],
            'status' => ['required', Rule::in(['scheduled', 'live', 'finished', 'cancelled'])],
        ];
    }
}
