<?php

namespace Src\Domain\Model;

use Illuminate\Support\Collection;

class PlayersCollection extends Collection
{
    public function hasAlreadyTaken(string $fieldValue, string $fieldName): bool
    {
        $fieldValues = $this->pluck($fieldName);

        if ($fieldValues->contains($fieldValue)) {
            return true;
        }

        return false;
    }

    public function isExist(string $playerKey): bool
    {
        return $this->some(fn (Player $player) => $player->key === $playerKey);
    }

    public function find(string $playerKey): Player
    {
        return $this->first(fn (Player $player) => $player->key === $playerKey);
    }

    public function indexOf(string $key): int
    {
        return $this->search(fn (Player $player) => $player->key === $key) + 1;
    }
}
