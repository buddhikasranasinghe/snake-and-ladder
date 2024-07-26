<?php

namespace Domain\Reports;

use Src\Domain\Model\Player;
use Illuminate\Support\Collection;
use Src\Domain\Model\PlayersCollection;

class PlayingOrderReport
{
    protected Collection $scores;
    protected Collection $playingOrder;
    protected PlayersCollection $players;

    public function __construct()
    {
        $this->playingOrder = new Collection;
    }

    public function get(Collection $scores, PlayersCollection $players): Collection
    {
        $this->scores = $scores;
        $this->players = $players;

        $this->makeScoredPlayersPositions();

        $this->makeNonScoredPlayersPositions();

        return $this->playingOrder;
    }

    protected function makeScoredPlayersPositions(): void
    {
        $orderedScores = $this->scores->sortByDesc('score');

        $position = 1;
        foreach ($orderedScores as $score) {
            $this->playingOrder->push([
                'position' => $position,
                'player' => $this->players->find($score['player_key']),
            ]);

            $position++;
        }
    }

    protected function makeNonScoredPlayersPositions(): void
    {
        $scoredPlayers = $this->scores->pluck('player_key');

        if ($this->players->count() === $scoredPlayers->count()) {
            return;
        }

        $nonScoredPlayers = $this->players->reject(
            fn(Player $player) => $scoredPlayers->contains($player->key)
        );

        foreach ($nonScoredPlayers as $player) {
            $this->playingOrder->push([
                'position' => null,
                'player' => $player,
            ]);
        }
    }
}
