<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\Domain\Model\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Src\Domain\Model\PlayersCollection;
use Domain\Exceptions\InvalidPlayerException;
use Domain\Actions\InitiallyDiceRollingAction;

class InitialRollingController extends Controller
{
    /**
     * @throws InvalidPlayerException
     */
    public function __invoke(InitiallyDiceRollingAction $action, Request $request): JsonResponse
    {
        $action->execute($this->findPlayer($request));

        return response()->json();
    }

    protected function findPlayer(Request $request): Player
    {
        $playerKey = $request->route('player_key');

        $players = PlayersCollection::wrap(Session::get('players'));

        return $players->find($playerKey);
    }
}
