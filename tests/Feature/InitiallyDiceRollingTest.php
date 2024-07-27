<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Src\Domain\Model\Player;
use Tests\Utilities\MakePlayers;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Storage;

class InitiallyDiceRollingTest extends TestCase
{
    use MakePlayers;

    protected Player $playerOne;
    protected Player $playerTwo;
    protected Player $playerThree;

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
        $players = $this->makePlayers(2);

        $this->playerOne = $players->toArray()[0];
        $this->playerTwo = $players->toArray()[1];
        $this->playerThree = $players->toArray()[2];
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
            ],
            [
                'players' => [
                    'playerOne',
                    'playerTwo',
                    'playerThree'
                ],
                'expectedOutcome' => [
                    [
                        'player' => 'playerOne',
                        'index' => 1
                    ],
                    [
                        'player' => 'playerTwo',
                        'index' => 2
                    ],
                    [
                        'player' => 'playerThree',
                        'index' => 3
                    ]
                ]
            ]
        ];
    }

    protected function assertInitiallyRollingScoreStored(TestResponse $response, array $expectedOutcome): void
    {
        $response->assertOk();

        $this->assertScoreUpdated($expectedOutcome);
    }

    protected function assertScoreUpdated(array $expectedOutcome): void
    {
        Storage::disk('dataSource')->exists('game.json');

        $storedScores = Storage::disk('dataSource')->json('game.json')['initial_rolling_scores'];

        $this->assertSameSize(
            $expectedOutcome,
            $storedScores
        );

        foreach ($expectedOutcome as $outcome) {
            foreach ($storedScores as $storedScore) {
                if ($storedScore['player_key'] === $this->{$outcome['player']}->key) {
                    $storedPlayerWithScore = $storedScore;
                }
            }

            $this->assertNotNull($storedPlayerWithScore['score']);
            $this->assertEquals($outcome['index'], $storedPlayerWithScore['player_index']);
        }
    }
}
