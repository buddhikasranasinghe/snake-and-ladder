<?php

namespace Domain\Actions;

use Domain\Model\PrevStage;
use Illuminate\Support\Facades\Session;

class FindPreviousStageAction
{
    public function execute(): PrevStage
    {
        $prevStage = new PrevStage();

        $prevStage->players = Session::get('players') ?? null;

        return $prevStage;
    }
}
