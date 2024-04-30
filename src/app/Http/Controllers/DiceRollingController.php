<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Src\Domain\Actions\DiceRollingAction;

class DiceRollingController extends Controller
{
    public function __invoke(DiceRollingAction $action): JsonResponse
    {
        return response()->json($action->execute(), Response::HTTP_OK);
    }
}
