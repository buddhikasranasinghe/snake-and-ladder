<?php

namespace Src\Domain\Actions;

use Src\Domain\Model\Player;
use Illuminate\Support\Facades\Storage;
use Domain\Exceptions\InvalidPlayerException;

class DiceRollingAction
{
    /**
     * @throws InvalidPlayerException
     */
    public function execute(Player $player): array
    {
        if (!$this->playerExist($player)) {
            throw InvalidPlayerException::playerNotFound();
        }

        $numbers = collect([1, 2, 3, 4, 5, 6]);

        return [
            'player' => $player,
            'score' => $numbers->random()
        ];
    }

    protected function playerExist(Player $player): bool
    {
        $players = Storage::disk('dataSource')->json('game.json')['players'];

        foreach ($players as $p) {
            if ($p['key'] === $player->key) {
                return true;
            }
        }

        return false;
    }
}
