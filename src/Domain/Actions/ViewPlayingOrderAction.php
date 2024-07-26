<?php

namespace Domain\Actions;

use Illuminate\Support\Collection;
use Domain\Reports\PlayingOrderReport;
use Illuminate\Support\Facades\Storage;
use Src\Domain\Model\PlayersCollection;

class ViewPlayingOrderAction
{
    public function __construct(protected PlayingOrderReport $report)
    {
    }

    public function execute(): Collection
    {
        $source = Storage::disk('dataSource')->json('game.json');

        $initialRollingScores = $source['initial_rolling_scores'] ?? [];
        $existingPlayers = $source['players'];

        return $this->report->get(
            Collection::wrap($initialRollingScores),
            (new PlayersCollection())->mapPlayers($existingPlayers)
        );
    }
}
