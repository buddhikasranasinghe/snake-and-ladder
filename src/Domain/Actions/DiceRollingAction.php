<?php

namespace Src\Domain\Actions;

use Src\Domain\Model\Player;
use Illuminate\Support\Facades\Session;
use Src\Domain\Model\PlayersCollection;
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
        $players = PlayersCollection::wrap(Session::get('players'));

        return $players->isExist($player->key);
    }
}
