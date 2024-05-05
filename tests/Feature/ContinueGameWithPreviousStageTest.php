<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Testing\TestResponse;

class ContinueGameWithPreviousStageTest extends TestCase
{
    /** @test */
    public function when_no_previous_stage()
    {
        $response = $this->getPrevStage();

        $this->assertNoPreviousStageReceived($response);
    }

    /** @test */
    public function when_players_generated_and_stopped()
    {
        $players = $this->makePlayers(numberOfPlayers: 2);

        $response = $this->getPrevStage();

        $this->assertGeneratedPlayersReceived($response, $players);
    }

    public function getPrevStage(): TestResponse
    {
        return $this->getJson('api/prev-stage');
    }

    protected function assertNoPreviousStageReceived(TestResponse $response): void
    {
        $expectedResponse = [
            'players' => null,
        ];

        $this->assertExpectedResponseReceived($response, $expectedResponse);
    }

    public function makePlayers(int $numberOfPlayers): array
    {
        $players = $this->postJson('api/make-players', [
            'number_of_players' => $numberOfPlayers,
        ]);

        return $players->json('players');
    }

    protected function assertGeneratedPlayersReceived(TestResponse $response, array $players): void
    {
        $expectedResponse = [
            'players' => $players,
        ];

        $this->assertExpectedResponseReceived($response, $expectedResponse);
    }

    public function assertExpectedResponseReceived(TestResponse $response, array $expectedResponse): void
    {
        $response->assertOk();

        $this->assertEquals(
            $expectedResponse,
            $response->json()
        );
    }
}
