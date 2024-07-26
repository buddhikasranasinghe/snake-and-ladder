<?php

namespace Src\Domain\Model;

class Player
{
    public string $key;
    public string $name;
    public string $pawnColour;
    public string $type;

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'pawnColour' => $this->pawnColour,
            'type' => $this->type,
        ];
    }
}
