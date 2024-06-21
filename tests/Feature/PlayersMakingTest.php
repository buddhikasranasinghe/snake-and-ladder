<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Storage;

class PlayersMakingTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidPayloadAndErrorsProvider
     */
    public function when_invalid_payload_given(mixed $payload, string $errors)
    {
        $response = $this->startGame($payload);

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
            $response = $this->startGame($players);

            $this->assertPlayersMade($response, $players);
        }
    }

    protected function startGame(mixed $payload): TestResponse
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

        $this->assertPlayersStored($response);
    }

    protected function assertPlayersReceived(TestResponse $response, $payload): void
    {
        $expectedPlayers = $payload + 1;

        $this->assertEquals($expectedPlayers, count($receivedPlayers = $response->Json('players')));

        $this->assertCount(
            1,
            Collection::wrap($response->json('players'))->where('type', 'human')
        );

        $this->assertCount(
            1,
            Collection::wrap($response->json('players'))->where('name', 'You')
        );

        foreach ($receivedPlayers as $player) {
            $this->assertNotNull($player['key']);
            $this->assertNotNull($player['name']);
            $this->assertNotNull($player['pawnColour']);
            $this->assertNotNull($player['type']);
        }
    }

    protected function assertPlayersStored(TestResponse $response): void
    {
        Storage::disk('dataSource')->assertExists('game.json');

        $storedPlayers = Storage::disk('dataSource')->json('game.json')['players'];

        foreach ($response->json('players') as $receivedPlayer) {
            foreach ($storedPlayers as $player) {
                if ($player['key'] === $receivedPlayer['key']) {
                    $storedplayer = $player;
                }
            }

            $this->assertSame($receivedPlayer['name'], $storedplayer['name']);
            $this->assertSame($receivedPlayer['pawnColour'], $storedplayer['pawnColour']);
        }
    }
}
