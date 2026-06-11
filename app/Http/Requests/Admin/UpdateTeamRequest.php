<?php

namespace App\Http\Requests\Admin;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Team $team */
        $team = $this->route('team');

        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:5', Rule::unique('teams', 'code')->ignore($team->id)],
            'group_name' => ['nullable', 'string', 'max:20'],
            'flag_url' => ['nullable', 'url', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
