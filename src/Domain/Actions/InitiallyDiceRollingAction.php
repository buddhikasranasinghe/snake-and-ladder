<?php

namespace Domain\Actions;

use Src\Domain\Model\Player;
use Illuminate\Support\Facades\Session;
use Src\Domain\Model\PlayersCollection;
use Src\Domain\Actions\DiceRollingAction;
use Domain\Exceptions\InvalidPlayerException;

class InitiallyDiceRollingAction
{
    public function __construct(
        protected DiceRollingAction $diceRollingAction
    ) {
    }

    /**
     * @throws InvalidPlayerException
     */
    public function execute(Player $player): void
    {
        $playerWithScore = $this->diceRollingAction->execute($player);

        $this->storeInitialRollingScores($playerWithScore);
    }

    protected function storeInitialRollingScores(array $playerWithScore): void
    {
        $players = PlayersCollection::wrap(Session::get('players'));

        $initialScoreWithPlayer = [
            'score' => $playerWithScore['score'],
            'player_key' => $playerWithScore['player']->key,
            'player_index' => $players->indexOf($playerWithScore['player']->key),
        ];

        if (Session::has('initial_rolling_scores')) {
            $initial_rolling_scores = Session::get('initial_rolling_scores');
            $initial_rolling_scores[] = $initialScoreWithPlayer;

            Session::put('initial_rolling_scores', $initial_rolling_scores);
        } else {
            Session::put('initial_rolling_scores', [$initialScoreWithPlayer]);
        }
    }
}
