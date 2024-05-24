<?php

namespace Tests\Utilities;

use Src\Domain\Model\PlayersCollection;
use Src\Domain\Actions\MakePlayersAction;
use Src\Domain\Commands\MakePlayersCommand;

trait MakePlayers
{
    public function makePlayers(int $numberOfPlayers): PlayersCollection
    {
        $command = new MakePlayersCommand();
        $command->numberOfPlayers = $numberOfPlayers;

        return app(MakePlayersAction::class)->execute($command);
    }
}
