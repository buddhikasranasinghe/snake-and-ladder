<?php

namespace App\Http\Requests;

use Src\Utilities\FormRequest;
use Src\Domain\Commands\MakePlayersCommand;

class MakePlayersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number_of_players' => [
                'required',
                'numeric',
                'min:1'
            ],
        ];
    }

    public function command(): MakePlayersCommand
    {
        $command = new MakePlayersCommand;

        $command->numberOfPlayers = $this->input('number_of_players');

        return $command;
    }
}
