<?php

namespace Src\Domain\Actions;

class DiceRollingAction
{
    public function execute(): int
    {
        $numbers = collect([1, 2, 3, 4, 5, 6]);

        return $numbers->random();
    }
}
