<?php

namespace Tests\Feature;

use Tests\TestCase;
use Src\Domain\Model\Player;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Session;

class PlayersMakingTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidPayloadAndErrorsProvider
     */
    public function when_invalid_payload_given(mixed $payload, string $errors)
    {
        $response = $this->makePlayers($payload);

        $this->assertValidationErrorsReceived($response, $errors);
    }

    public static function invalidPayloadAndErrorsProvider(): array
    {
        return [
            [
                'payload' => '',
                'errors' => 'The number of players field is required.'
            ],
            [
                'payload' => 'player count',
                'errors' => 'The number of players field must be a number.'
            ],
            [
                'payload' => 0,
                'errors' => 'The number of players field must be at least 1.'
            ]
        ];
    }

    /** @test */
    public function when_players_count_given()
    {
        foreach (range(1,5) as $players) {
            $response = $this->makePlayers($players);

            $this->assertPlayersMade($response, $players);
        }
    }

    protected function makePlayers(mixed $payload): TestResponse
    {
        return $this->postJson('api/make-players', [
            'number_of_players' => $payload,
        ]);
    }

    protected function assertValidationErrorsReceived(TestResponse $response, string $errorMessage): void
    {
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'number_of_players' => $errorMessage
        ]);
    }

    protected function assertPlayersMade(TestResponse $response, int $payload): void
    {
        $response->assertOk();

        $this->assertPlayersReceived($response, $payload);

        $this->assertSessionUpdated($response);
    }

    protected function assertPlayersReceived(TestResponse $response, $payload): void
    {
        $expectedPlayers = $payload + 1;

        $this->assertEquals($expectedPlayers, count($receivedPlayers = $response->Json('players')));

        $this->assertCount(
            1,
            Collection::wrap($response->json('players'))->where('type', 'human')
        );

        foreach ($receivedPlayers as $player) {
            $this->assertNotNull($player['key']);
            $this->assertNotNull($player['name']);
            $this->assertNotNull($player['pawnColour']);
            $this->assertNotNull($player['type']);
        }
    }

    protected function assertSessionUpdated(TestResponse $response): void
    {
        $this->assertNotNull(Session::get('players'));

        $playersOnSession = Collection::wrap(Session::get('players'));

        foreach ($response->json('players') as $receivedPlayer) {
            $storedplayer = $playersOnSession->filter(fn (Player $player) => $player->key === $receivedPlayer['key']);

            $this->assertSame($receivedPlayer['name'], $storedplayer->first()->name);
            $this->assertSame($receivedPlayer['pawnColour'], $storedplayer->first()->pawnColour);
        }
    }
}
