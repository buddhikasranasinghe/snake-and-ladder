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
}
