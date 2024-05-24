<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\Domain\Model\Player;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Src\Domain\Model\PlayersCollection;
use Illuminate\Support\Facades\Session;
use Src\Domain\Actions\DiceRollingAction;
use Domain\Exceptions\InvalidPlayerException;

class DiceRollingController extends Controller
{
    public function __invoke(Request $request, DiceRollingAction $action): JsonResponse
    {
        try {
            $score = $action->execute($this->findPlayer($request));

            return response()->json($score, Response::HTTP_OK);
        } catch (InvalidPlayerException $e) {
            return response()->json($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    protected function findPlayer(Request $request): Player
    {
        $playerKey = $request->route('player_key');

        $players = PlayersCollection::wrap(Session::get('players'));

        return $players->find($playerKey);
    }
}
