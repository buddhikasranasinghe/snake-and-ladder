<?php

namespace Src\Utilities;

use Src\Domain\Model\Player;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getPlayer(): ?Player
    {
        $playerKey = $this->route('player_key');

        $players = Storage::disk('dataSource')->json('game.json')['players'];

        foreach ($players as $p) {
            if ($p['key'] === $playerKey) {
                $player = new Player();
                $player->key = $p['key'];
                $player->name = $p['name'];
                $player->pawnColour = $p['pawnColour'];
                $player->type = $p['type'];

                return $player;
            }
        }

        return null;
    }
}
