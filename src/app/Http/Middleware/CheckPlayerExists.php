<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Src\Domain\Model\PlayersCollection;
use Symfony\Component\HttpFoundation\Response;

class CheckPlayerExists
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isPlayerValid($request)) {
            return response()->json(
                [
                    'errors' => [
                        'player_key' => [
                            'The given player not found',
                        ]
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $next($request);
    }

    protected function isPlayerValid(Request $request): bool
    {
        $players = PlayersCollection::wrap(Session::get('players'));

        if (! $players) {
            return false;
        }

        return $players->isExist($request->route('player_key'));
    }
}
