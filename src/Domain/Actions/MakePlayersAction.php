<?php

namespace Src\Domain\Actions;

use Illuminate\Support\Str;
use Src\Domain\Model\Player;
use Domain\Enums\PlayerTypes;
use Illuminate\Support\Collection;
use Src\Domain\Model\PlayersCollection;
use Illuminate\Support\Facades\Storage;
use Src\Domain\Commands\MakePlayersCommand;

class MakePlayersAction
{
    public function __construct(protected PlayersCollection $players)
    {
        $this->players = new PlayersCollection;
    }

    public function execute(MakePlayersCommand $command): Collection
    {
        $this->makeHumanPlayer();

        $this->makeAIPlayers($command);

        $this->storePlayers();

        return $this->players;
    }

    protected function makeHumanPlayer(): void
    {
        $this->players->push($this->makePlayer(PlayerTypes::HUMAN, 'You'));
    }

    protected function makeAIPlayers(MakePlayersCommand $command): void
    {
        for ($i = 0; $i < $command->numberOfPlayers; $i++) {
            $this->players->push($this->makePlayer(PlayerTypes::AI));
        }
    }

    protected function makePlayer(PlayerTypes $type, string $name = null): Player
    {
        $player = new Player;

        $player->key = $this->generateKey();
        $player->name = $name ?? $this->generateName();
        $player->pawnColour = $this->generateColor();
        $player->type = $type->value;

        return $player;
    }

    protected function generateKey(): string
    {
        while (true) {
            $key = Str::uuid();

            if (!$this->players->hasAlreadyTaken($key, 'key')) {
                return $key;
            }
        }
    }

    protected function generateName(): string
    {
        while (true) {
            $name = fake()->name();

            if (!$this->players->hasAlreadyTaken($name, 'name')) {
                return $name;
            }
        }
    }

    protected function generateColor(): string
    {
        while (true) {
            $colour = fake()->hexColor();

            if (!$this->players->hasAlreadyTaken($colour, 'pawnColour')) {
                return $colour;
            }
        }
    }

    protected function storePlayers(): void
    {
        Storage::put(
            'dataSource/game.json',
            json_encode([
                'players' => $this->players->toArray()
            ])
        );
    }
}
