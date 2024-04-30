<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\MakePlayersRequest;
use Src\Domain\Actions\MakePlayersAction;

class MakePlayersController extends Controller
{
    public function __invoke(MakePlayersRequest $request, MakePlayersAction $action): JsonResponse
    {
        return response()->json([
            'players' => $action->execute($request->command())->toArray()
        ]);
    }
}
