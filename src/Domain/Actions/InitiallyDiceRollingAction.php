<?php

namespace Domain\Actions;

use Src\Domain\Model\Player;
use Illuminate\Support\Facades\Storage;
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
    public function execute(Player $player): array
    {
        $playerWithScore = $this->diceRollingAction->execute($player);

        $this->storeInitialRollingScores($playerWithScore);

        return $playerWithScore;
    }

    protected function storeInitialRollingScores(array $playerWithScore): void
    {
        $initialScoreWithPlayer = [
            'score' => $playerWithScore['score'],
            'player_key' => $playerWithScore['player']->key,
            'player_index' => $this->getPlayerIndex($playerWithScore['player']->key),
        ];

        $source = Storage::disk('dataSource')->json('game.json');

        if ($this->hasInitialRollingScores()) {
            $scores = $source['initial_rolling_scores'];

            $scores[] = $initialScoreWithPlayer;

            $source['initial_rolling_scores'] = $scores;
        } else {
            $source['initial_rolling_scores'][] = $initialScoreWithPlayer;
        }

        Storage::disk('dataSource')->put('game.json', json_encode($source));
    }

    protected function getPlayerIndex(string $playerKey): int
    {
        $players = Storage::disk('dataSource')->json('game.json')['players'];

        foreach ($players as $key => $player) {
            if ($player['key'] === $playerKey) {
                return $key + 1;
            }
        }

        return 0;
    }

    protected function hasInitialRollingScores(): bool
    {
        return array_key_exists(
            'initial_rolling_scores',
            Storage::disk('dataSource')->json('game.json')
        );
    }
}
