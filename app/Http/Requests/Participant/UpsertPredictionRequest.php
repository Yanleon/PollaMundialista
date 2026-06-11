<?php

namespace App\Http\Requests\Participant;

use App\Models\MatchGame;
use Illuminate\Foundation\Http\FormRequest;

class UpsertPredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'predicted_home_score' => ['required', 'integer', 'min:0', 'max:30'],
            'predicted_away_score' => ['required', 'integer', 'min:0', 'max:30'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var MatchGame|null $matchGame */
            $matchGame = $this->route('matchGame');

            if (! $matchGame) {
                $validator->errors()->add('match_game', 'Partido no encontrado.');

                return;
            }

            if (! $matchGame->isPredictionOpen()) {
                $validator->errors()->add('prediction_deadline', 'La fecha limite para pronosticar ya finalizo.');
            }
        });
    }
}
