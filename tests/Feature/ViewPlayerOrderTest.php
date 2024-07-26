<?php

namespace Feature;

use Tests\TestCase;
use Src\Domain\Model\Player;
use Tests\Utilities\MakePlayers;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Storage;

class ViewPlayerOrderTest extends TestCase
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
    public function when_not_scored()
    {
        $response = $this->getReport();

        $this->assertDoNotSeePlyingOrder($response);
    }

    /**
     * @test
     * @dataProvider scoreWithOrderProvider
     */
    public function when_players_score(array $initialRollingScores, array $expectedPlayingOrder)
    {
        $this->createPlayers();

        $scores = $this->makeScores($initialRollingScores);

        $this->initialRolling($scores->toArray());

        $response = $this->getReport();

        $this->assertSeeExpectedPlayingOrder($response, $expectedPlayingOrder);
    }

    protected function createPlayers(): void
    {
        $players = $this->makePlayers(2);

        $this->playerOne = $players[0];
        $this->playerTwo = $players[1];
        $this->playerThree = $players[2];
    }

    protected function initialRolling(array $initialRollingScores): void
    {
        $source = Storage::disk('dataSource')->json('game.json');

        $source['initial_rolling_scores'] = $initialRollingScores;

        Storage::disk('dataSource')->put('game.json', json_encode($source));
    }

    protected function getReport(): TestResponse
    {
        return $this->getJson('api/playing-order-report');
    }

    protected function assertDoNotSeePlyingOrder(TestResponse $response): void
    {
        $expectedPlayingOrder = [
            [
                "position" => null,
                "player" => 'playerOne',
            ],[
                "position" => null,
                "player" => 'playerTwo'
            ],[
                "position" => null,
                "player" => 'playerThree'
            ],
        ];

        $this->assertSeeExpectedPlayingOrder($response, $expectedPlayingOrder);
    }

    public static function scoreWithOrderProvider(): array
    {
        return [
            'a player score' => [
                'initialRollingScores' => [
                    [
                        "score" => 6,
                        "player" => 'playerOne',
                        "index" => 1
                    ]
                ],
                'expectedPlayingOrder' => [
                    [
                        "position" => 1,
                        "player" => 'playerOne',
                    ],[
                        "position" => null,
                        "player" => 'playerTwo',
                    ],[
                        "position" => null,
                        "player" => 'playerThree',
                    ]
                ]
            ],
            'some players score' => [
                'initialRollingScores' => [
                    [
                        "score" => 6,
                        "player" => 'playerOne',
                        "index" => 1
                    ],[
                        "score" => 2,
                        "player" => 'playerTwo',
                        "index" => 2
                    ]
                ],
                'expectedPlayingOrder' => [
                    [
                        "position" => 1,
                        "player" => 'playerOne',
                    ],[
                        "position" => 2,
                        "player" => 'playerTwo',
                    ],[
                        "position" => null,
                        "player" => 'playerThree',
                    ]
                ]
            ],
            'score difference scores' => [
                'initialRollingScores' => [
                    [
                        "score" => 6,
                        "player" => 'playerOne',
                        "index" => 1
                    ],[
                        "score" => 2,
                        "player" => 'playerTwo',
                        "index" => 2
                    ],[
                        "score" => 5,
                        "player" => 'playerThree',
                        "index" => 3
                    ]
                ],
                'expectedPlayingOrder' => [
                    [
                        "position" => 1,
                        "player" => 'playerOne',
                    ],[
                        "position" => 2,
                        "player" => 'playerThree',
                    ],[
                        "position" => 3,
                        "player" => 'playerTwo',
                    ]
                ]
            ],
            'score same score' => [
                'initialRollingScores' => [
                    [
                        "score" => 6,
                        "player" => 'playerOne',
                        "index" => 1
                    ],[
                        "score" => 2,
                        "player" => 'playerTwo',
                        "index" => 2
                    ],[
                        "score" => 6,
                        "player" => 'playerThree',
                        "index" => 3
                    ]
                ],
                'expectedPlayingOrder' => [
                    [
                        "position" => 1,
                        "player" => 'playerOne',
                    ],[
                        "position" => 2,
                        "player" => 'playerThree',
                    ],[
                        "position" => 3,
                        "player" => 'playerTwo',
                    ]
                ]
            ]
        ];
    }

    protected function makeScores(array $initialRollingScores): Collection
    {
        $scores = new Collection;

        foreach ($initialRollingScores as $initialRollingScore) {
            $scores->push([
                'score' => $initialRollingScore['score'],
                'player_key' => $this->{$initialRollingScore['player']}->key,
                'player_index' => $initialRollingScore['index']
            ]);
        }

        return $scores;
    }

    protected function assertSeeExpectedPlayingOrder(
        TestResponse $response,
        array $expectedPlayingOrder
    ): void {
        $response->assertOK();

        $expectedOrder = $this->makeExpectedOrder($expectedPlayingOrder);

        $this->assertSame(
            $expectedOrder->toArray(),
            $response->json('report')
        );
    }

    protected function makeExpectedOrder(array $expectedOrder): Collection
    {
        $playingOrder = new Collection;

        foreach ($expectedOrder as $order) {
            $playingOrder->push([
                'position' => $order['position'],
                'player' => $this->{$order['player']}->toArray(),
            ]);
        }

        return $playingOrder;
    }
}
