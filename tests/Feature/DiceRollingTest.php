<?php

namespace Tests\Feature;

use Src\Domain\Model\Player;
use Tests\TestCase;
use Illuminate\Support\Str;
use Tests\Utilities\MakePlayers;
use Illuminate\Testing\TestResponse;

class DiceRollingTest extends TestCase
{
    use MakePlayers;

    /** @test */
    public function when_invalid_player_given()
    {
        $playerKey = Str::uuid();

        $response = $this->diceRolling($playerKey);

        $this->assertNotScored($response);
    }

    /** @test */
    public function when_dice_rolling()
    {
        $players = $this->makePlayers(2);

        $response = $this->diceRolling($players->first()->key);

        $this->assertScoreReceived($response, $players->first());
    }

    public function diceRolling(string $playerKey): TestResponse
    {
        return $this->getJson('api/dice-rolling/'.$playerKey);
    }

    protected function assertNotScored(TestResponse $response): void
    {
        $response->assertUnprocessable();

        $this->assertSame(
            'The given player not found',
            $response->json('errors.player_key.0')
        );
    }

    protected function assertScoreReceived(TestResponse $response, Player $player): void
    {
        $response->assertOk();

        $this->assertLessThan(7, $randomValue = $response->json('score'));
        $this->assertGreaterThan(0, $randomValue);
        $this->assertIsInt($randomValue);

        $this->assertSame(
            $player->key,
            $response->json('player')['key']
        );
    }
}
