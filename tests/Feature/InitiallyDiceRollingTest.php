<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Src\Domain\Model\Player;
use Tests\Utilities\MakePlayers;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Session;

class InitiallyDiceRollingTest extends TestCase
{
    use MakePlayers;

    protected Player $playerOne;
    protected Player $playerTwo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createPlayers();
    }

    /** @test */
    public function when_invalid_payload_given()
    {
        $response = $this->getJson('api/initial-rolling/'.Str::uuid());

        $this->assertSeeValidationErrors($response);
    }

    /**
     * @test
     *
     * @dataProvider playersAndExpectedOutputProvider
     */
    public function when_initially_rolling_dice(array $players, array $expectedOutcome)
    {
        foreach ($players as $player) {
            $response = $this->getJson('api/initial-rolling/'.$this->{$player}->key);
        }

        $this->assertInitiallyRollingScoreStored($response, $expectedOutcome);
    }

    protected function createPlayers(): void
    {
        $players = $this->makePlayers(1);

        $this->playerOne = $players->first();
        $this->playerTwo = $players->last();
    }

    protected function assertSeeValidationErrors(TestResponse $response): void
    {
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('player_key');
    }

    public static function playersAndExpectedOutputProvider(): array
    {
        return [
            [
                'players' => [
                    'playerOne'
                ],
                'expectedOutcome' => [
                    [
                        'player' => 'playerOne',
                        'index' => 1
                    ]
                ]
            ],
            [
                'players' => [
                    'playerOne',
                    'playerTwo'
                ],
                'expectedOutcome' => [
                    [
                        'player' => 'playerOne',
                        'index' => 1
                    ],
                    [
                        'player' => 'playerTwo',
                        'index' => 2
                    ]
                ]
            ]
        ];
    }

    protected function assertInitiallyRollingScoreStored(TestResponse $response, array $expectedOutcome): void
    {
        $response->assertOk();

        $this->assertSessionUpdatedWithExpectedOutcome($expectedOutcome);
    }

    protected function assertSessionUpdatedWithExpectedOutcome(array $expectedOutcome): void
    {
        $this->assertNotNull(Session::get('initial_rolling_scores'));

        $scoresOnSession = Collection::wrap(Session::get('initial_rolling_scores'));

        $this->assertSameSize(
            $expectedOutcome,
            $scoresOnSession
        );

        foreach ($expectedOutcome as $outcome) {
            $storedPlayerWithScore = $scoresOnSession->filter(
                fn ($playerWithScore) => $playerWithScore['player_key'] === $this->{$outcome['player']}->key
            );

            $this->assertNotNull($storedPlayerWithScore->first()['score']);
            $this->assertEquals($outcome['index'], $storedPlayerWithScore->first()['player_index']);
        }
    }
}
